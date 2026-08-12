const { useState, useEffect } = React;

const DiscussionBoard = ({ projectId, currentUserId, isTeamMember }) => {
    const [isLoading, setIsLoading] = useState(true);
    const [discussions, setDiscussions] = useState([]);
    
    // Input States
    const [mainInput, setMainInput] = useState('');
    const [editContent, setEditContent] = useState('');
    const [replyInputs, setReplyInputs] = useState({});
    
    // Interaction States
    const [replyingTo, setReplyingTo] = useState(null);
    const [editingId, setEditingId] = useState(null);
    const [attachedTask, setAttachedTask] = useState(null);
    const [activeMenuId, setActiveMenuId] = useState(null); // NEW: Tracks which '...' menu is open
    
    // Reply Toggle State
    const [expandedReplies, setExpandedReplies] = useState({});

    // NEW: Close the popup menu if the user clicks anywhere else on the page
    useEffect(() => {
        const handleClickOutside = (e) => {
            if (!e.target.closest('.comment-options-wrapper')) {
                setActiveMenuId(null);
            }
        };
        document.addEventListener('click', handleClickOutside);
        return () => document.removeEventListener('click', handleClickOutside);
    }, []);

    const toggleReplies = (threadId) => {
        setExpandedReplies(prev => ({
            ...prev,
            [threadId]: !prev[threadId]
        }));
    };

    const fetchDiscussions = async () => {
        try {
            const res = await fetch(`/src/modules/discussions/discussion-controller.php?project_id=${projectId}`);
            const json = await res.json();
            if (json.success) setDiscussions(json.data);
        } catch (error) {
            console.error('Error fetching discussions:', error);
        } finally {
            setIsLoading(false); 
        }
    };

    useEffect(() => {
        fetchDiscussions();

        // 1. Setup Supabase Realtime
        const uniqueChannelName = `public:discussions:${projectId}-${Date.now()}`;
        const channel = supabaseClient
            .channel(uniqueChannelName)
            .on('postgres_changes', { event: '*', schema: 'public', table: 'discussions', filter: `project_id=eq.${projectId}` }, 
                (payload) => fetchDiscussions()
            ).subscribe();

        // 2. MAGIC FIX: Force the parent PHP containers into a Flexbox Chat Layout
        const rootNode = document.getElementById('react-discussion-root');
        if (rootNode) {
            rootNode.style.display = 'flex';
            rootNode.style.flexDirection = 'column';
            rootNode.style.flex = '1';
            rootNode.style.minHeight = '0';
            rootNode.style.overflowX = 'hidden'; // NEW: Prevents horizontal scrolling
            
            const card = rootNode.closest('.card');
            if (card) {
                card.style.display = 'flex';
                card.style.flexDirection = 'column';
                card.style.overflow = 'hidden'; // Stop the whole card from scrolling so the input stays fixed
            }
        }

        return () => supabaseClient.removeChannel(channel);
    }, [projectId]);

    const handleAction = async (action, payload) => {
        const formData = new FormData();
        formData.append('action', action);
        Object.entries(payload).forEach(([key, value]) => {
            if (value !== null) formData.append(key, value);
        });

        try {
            const res = await fetch('/src/modules/discussions/discussion-controller.php', {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            if (json.success) {
                fetchDiscussions(); 
                return true;
            } else {
                alert('Error: ' + json.message);
                return false;
            }
        } catch (error) {
            console.error('Action error:', error);
            return false;
        }
    };

    const submitComment = async (parentId = null) => {
        const content = parentId ? replyInputs[parentId] : mainInput;
        if (!content?.trim() && !attachedTask) return alert("Please enter a comment or attach a task.");

        const success = await handleAction('add', {
            project_id: projectId,
            content: content,
            parent_id: parentId,
            task_id: !parentId && attachedTask ? attachedTask.id : null
        });

        if (success) {
            if (!parentId) {
                setMainInput('');
                setAttachedTask(null);
            } else {
                setReplyInputs(prev => ({...prev, [parentId]: ''}));
                setReplyingTo(null);
                setExpandedReplies(prev => ({...prev, [parentId]: true}));
            }
        }
    };

    const deleteComment = async (id) => {
        if (!window.confirm("Are you sure you want to delete this comment?")) return;
        await handleAction('delete', { discussion_id: id });
    };

    const renderCommentNode = (node, isReply = false) => {
        const isEditing = editingId === node.id;
        
        return (
            <div key={node.id} className={`thread-layout ${isReply ? 'reply-layout' : ''}`}>
                <div className="thread-spine">
                    <img src={node.avatar_url || '/default-avatar.png'} alt="avatar" style={{width: '40px', borderRadius: '50%'}} />
                    {!isReply && node.replies?.length > 0 && <div className="thread-line"></div>}
                </div>
                
                <div className="thread-content">
                    <div className="comment-header flex-header">
                        <span className="timeline-user">{node.first_name} {node.last_name}</span>
                        <span className="comment-meta">· {node.formatted_time || ''}</span>
                        
                        {!isTeamMember && node.flag_status && !isReply && (
                            <span className={`badge ${node.flag_status === 'solved' ? 'completed' : 'attention'} badge-right`}>
                                {node.flag_status === 'solved' ? 'Solved' : 'Needs Attention'}
                            </span>
                        )}
                    </div>
                    
                    {!isEditing ? (
                        <div id={`comment-display-${node.id}`}>
                            {node.formatted_content && (
                                <p 
                                    className="comment-body thread-body" 
                                    dangerouslySetInnerHTML={{ __html: node.formatted_content }} 
                                />
                            )}
                            
                            {node.task_id && (
                                <div 
                                    className="attached-task-folder clickable-task-badge" 
                                    onClick={() => window.scrollToTask && window.scrollToTask(node.task_id)}
                                    style={{
                                        marginTop: '12px', 
                                        display: 'inline-flex', 
                                        alignItems: 'center', 
                                        gap: '10px',
                                        padding: '10px 16px', 
                                        borderRadius: '12px', 
                                        background: 'rgba(255, 255, 255, 0.8)', 
                                        border: '1px solid var(--border-color)',
                                        boxShadow: '0 2px 4px rgba(0,0,0,0.02)',
                                        cursor: 'pointer',
                                        transition: 'all 0.2s ease'
                                    }}
                                    onMouseOver={(e) => { e.currentTarget.style.borderColor = 'var(--primary)'; e.currentTarget.style.transform = 'translateY(-1px)'; }}
                                    onMouseOut={(e) => { e.currentTarget.style.borderColor = 'var(--border-color)'; e.currentTarget.style.transform = 'none'; }}
                                >
                                    <span style={{fontSize: '1.2rem', filter: 'grayscale(100%)', opacity: '0.6'}}>📁</span>
                                    <strong style={{color: 'var(--text-main)', fontSize: '0.95rem'}}>{node.task_title || 'Unknown Task'}</strong>
                                    <i className="ph ph-arrow-up-right" style={{color: 'var(--text-muted)', marginLeft: '4px'}}></i>
                                </div>
                            )}
                        </div>
                    ) : (
                        <div style={{marginTop: '0.5rem', position: 'relative'}}>
                            <textarea 
                                className="comment-input compact-input" 
                                style={{width: '100%', minHeight: '60px'}}
                                value={editContent}
                                onChange={(e) => setEditContent(e.target.value)}
                            />
                            <div style={{display: 'flex', justifyContent: 'flex-end', gap: '8px', marginTop: '8px'}}>
                                <button className="btn-primary btn-sm" style={{background: 'transparent', color: 'var(--text-muted)', border: '1px solid var(--border-color)'}} onClick={() => setEditingId(null)}>Cancel</button>
                                <button className="btn-primary btn-sm" onClick={() => {
                                    handleAction('edit', { discussion_id: node.id, content: editContent }).then(() => setEditingId(null));
                                }}>Save</button>
                            </div>
                        </div>
                    )}
                    
                    {!isEditing && (
                        <div className="comment-action-bar" style={{marginTop: isReply ? '6px' : '10px', flexWrap: 'wrap', gap: '8px'}}>
                            
                            {/* 1. Flags (Attention & Solved) */}
                            {!isReply && isTeamMember && (
                                <React.Fragment>
                                    <button className={`action-btn always-visible ${node.flag_status === 'attention' ? 'active-attention' : ''}`} onClick={() => handleAction('flag', { discussion_id: node.id, status: node.flag_status === 'attention' ? '' : 'attention' })}>
                                        <i className={`ph ${node.flag_status === 'attention' ? 'ph-warning-circle-fill' : 'ph-warning-circle'}`}></i> Attention
                                    </button>
                                    <button className={`action-btn always-visible ${node.flag_status === 'solved' ? 'active-solved' : ''}`} onClick={() => handleAction('flag', { discussion_id: node.id, status: node.flag_status === 'solved' ? '' : 'solved' })}>
                                        <i className={`ph ${node.flag_status === 'solved' ? 'ph-check-circle-fill' : 'ph-check-circle'}`}></i> Solved
                                    </button>
                                </React.Fragment>
                            )}

                            {/* 2. Reply */}
                            {!isReply && (
                                <button className="action-btn always-visible" onClick={() => setReplyingTo(replyingTo === node.id ? null : node.id)}>
                                    <i className="ph ph-chat-circle"></i> Reply
                                </button>
                            )}

                            {/* Subtle Divider (Only shows if there are personal actions next to thread actions) */}
                            {node.user_id === currentUserId && (!isReply) && (
                                <div style={{width: '1px', height: '14px', background: 'var(--border-color)', margin: '0 4px', alignSelf: 'center'}}></div>
                            )}

                            {/* 3. Options Menu (...) for Edit & Delete */}
                            {node.user_id === currentUserId && (
                                <div className="comment-options-wrapper" style={{position: 'relative', display: 'inline-block'}}>
                                    <button 
                                        className="action-btn always-visible" 
                                        onClick={(e) => {
                                            e.stopPropagation(); // Prevents the outside-click listener from instantly closing it
                                            setActiveMenuId(activeMenuId === node.id ? null : node.id);
                                        }}
                                    >
                                        <i className="ph ph-dots-three"></i>
                                    </button>
                                    
                                    {/* The Popup Dropdown */}
                                    <div 
                                        className={`dropdown-menu ${activeMenuId === node.id ? 'active' : ''}`} 
                                        style={{
                                            width: '130px', 
                                            top: 'calc(100% + 4px)',
                                            right: '0', /* FIX: Anchors to the right edge so it opens inward */
                                            left: 'auto', 
                                            padding: '6px',
                                            zIndex: 1050 /* Ensures it sits above all other list items */
                                        }}
                                    >
                                        <div 
                                            className="dropdown-item" 
                                            onClick={() => { setEditingId(node.id); setEditContent(node.content); setActiveMenuId(null); }}
                                            style={{padding: '8px 12px', cursor: 'pointer', fontSize: '0.85rem'}}
                                        >
                                            <i className="ph ph-pencil-simple"></i> Edit
                                        </div>
                                        <div 
                                            className="dropdown-item" 
                                            onClick={() => { deleteComment(node.id); setActiveMenuId(null); }}
                                            style={{padding: '8px 12px', cursor: 'pointer', fontSize: '0.85rem', color: 'var(--status-attention)'}}
                                        >
                                            <i className="ph ph-trash"></i> Delete
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    )}

                    {replyingTo === node.id && (
                        <div className="reply-form-container" style={{display: 'flex', marginTop: '10px', position: 'relative'}}>
                            <input 
                                type="text" 
                                className="comment-input compact-input" 
                                placeholder="Post your reply..." 
                                value={replyInputs[node.id] || ''}
                                onChange={(e) => setReplyInputs(prev => ({...prev, [node.id]: e.target.value}))}
                                style={{flexGrow: 1}}
                            />
                            <button className="btn-primary btn-sm" onClick={() => submitComment(node.id)}>Reply</button>
                        </div>
                    )}
                </div>
            </div>
        );
    };

    return (
        <div style={{ display: 'flex', flexDirection: 'column', flex: 1, minHeight: 0, height: '100%' }}>
            
            {/* The scrolling comments container */}
            <div className="discussion-list" style={{ flex: 1, overflowY: 'auto', overflowX: 'hidden', paddingRight: '4px', marginBottom: '1rem' }}>
                {isLoading ? (
                    <div style={{textAlign: 'center', padding: '2rem 0', color: 'var(--text-muted)'}}>
                        <p>Loading discussions...</p>
                    </div>
                ) : discussions.length === 0 ? (
                    <p style={{color: 'var(--text-muted)', fontSize: '0.9rem', textAlign: 'center', padding: '1rem 0'}}>No discussions yet. Start the conversation!</p>
                ) : (
                    discussions.map(thread => (
                        <div key={thread.id} className="comment-thread">
                            {renderCommentNode(thread)}
                            
                            {thread.replies && thread.replies.length > 0 && (
                                <div className="replies-container">
                                    {thread.replies.length > 1 ? (
                                        !expandedReplies[thread.id] ? (
                                            <button 
                                                onClick={() => toggleReplies(thread.id)}
                                                style={{
                                                    background: 'transparent',
                                                    border: 'none',
                                                    color: 'var(--primary)',
                                                    fontSize: '0.85rem',
                                                    fontWeight: '600',
                                                    cursor: 'pointer',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    gap: '6px',
                                                    padding: '6px 8px',
                                                    marginLeft: '-8px', // FIX: Pulls left to align perfectly with comment text
                                                    marginTop: '4px',
                                                    borderRadius: '8px',
                                                    transition: 'background 0.2s ease'
                                                }}
                                                onMouseOver={(e) => e.currentTarget.style.background = 'rgba(0, 102, 204, 0.05)'}
                                                onMouseOut={(e) => e.currentTarget.style.background = 'transparent'}
                                            >
                                                <i className="ph ph-caret-down" style={{fontSize: '1rem'}}></i> 
                                                Show {thread.replies.length} replies
                                            </button>
                                        ) : (
                                            <>
                                                {thread.replies.map(reply => renderCommentNode(reply, true))}
                                                <button 
                                                    onClick={() => toggleReplies(thread.id)}
                                                    style={{
                                                        background: 'transparent',
                                                        border: 'none',
                                                        color: 'var(--text-muted)',
                                                        fontSize: '0.85rem',
                                                        fontWeight: '600',
                                                        cursor: 'pointer',
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        gap: '6px',
                                                        padding: '6px 8px',
                                                        marginLeft: '-8px', // FIX: Pulls left to align perfectly with comment text
                                                        marginTop: '4px',
                                                        borderRadius: '8px',
                                                        transition: 'background 0.2s ease'
                                                    }}
                                                    onMouseOver={(e) => e.currentTarget.style.background = 'rgba(0, 0, 0, 0.03)'}
                                                    onMouseOut={(e) => e.currentTarget.style.background = 'transparent'}
                                                >
                                                    <i className="ph ph-caret-up" style={{fontSize: '1rem'}}></i> 
                                                    Hide replies
                                                </button>
                                            </>
                                        )
                                    ) : (
                                        thread.replies.map(reply => renderCommentNode(reply, true))
                                    )}
                                </div>
                            )}
                            <hr className="thread-divider" />
                        </div>
                    ))
                )}
            </div>

            {/* The fixed input container */}
            <div 
                className="floating-thread-box" 
                style={{
                    flexShrink: 0,
                    backgroundColor: 'var(--surface-color, #ffffff)', // Fills space perfectly
                    paddingTop: '16px',
                    borderTop: '1px solid rgba(0,0,0,0.06)',
                    marginTop: 'auto'
                }}
            >
                {attachedTask && (
                    <div style={{
                        display: 'flex', 
                        alignItems: 'center', 
                        gap: '10px',
                        background: 'rgba(255, 255, 255, 0.95)', 
                        padding: '8px 14px', 
                        borderRadius: '12px', 
                        marginBottom: '10px', 
                        width: 'fit-content', 
                        border: '1px dashed var(--primary)',
                        boxShadow: '0 4px 12px rgba(0, 102, 204, 0.08)'
                    }}>
                        <span style={{fontSize: '1.2rem', filter: 'grayscale(100%)', opacity: '0.6'}}>📁</span>
                        <strong style={{color: 'var(--primary)', fontSize: '0.9rem'}}>{attachedTask.title}</strong>
                        <button onClick={() => setAttachedTask(null)} style={{border: 'none', background: 'transparent', color: 'var(--status-attention)', cursor: 'pointer', padding: '0 0 0 8px', display: 'flex', alignItems: 'center'}}>
                            <i className="ph ph-x-circle" style={{fontSize: '1.15rem'}}></i>
                        </button>
                    </div>
                )}
                
                <div 
                    id="discussion-drop-zone" 
                    className="chat-input-wrapper drop-zone" 
                    style={{position: 'relative', transition: 'all 0.25s ease'}} 
                    onDragOver={(e) => e.preventDefault()} 
                    onDrop={(e) => {
                        e.preventDefault();
                        
                        e.currentTarget.style.border = '';
                        e.currentTarget.style.background = '';
                        e.currentTarget.style.boxShadow = '';
                        const input = e.currentTarget.querySelector('textarea');
                        if (input && input.dataset.originalPlaceholder) {
                            input.placeholder = input.dataset.originalPlaceholder;
                        }

                        try {
                            const data = JSON.parse(e.dataTransfer.getData('application/json'));
                            if (data && data.id) setAttachedTask(data);
                        } catch (err) {
                            console.error("Failed to parse dragged task data", err);
                        }
                    }}
                >
                    <textarea 
                        className="minimal-chat-input" 
                        placeholder="Type a comment or drag a task here..." 
                        rows="1" 
                        value={mainInput}
                        onChange={(e) => setMainInput(e.target.value)}
                        onInput={(e) => { e.target.style.height = ''; e.target.style.height = e.target.scrollHeight + 'px' }}
                    />
                    <button className="btn-primary minimal-send-btn" onClick={() => submitComment()} title="Post">
                        <i className="ph ph-paper-plane-right"></i>
                    </button>
                </div>
            </div>
        </div>
    );
};

const rootElement = document.getElementById('react-discussion-root');

if (rootElement) {
    const projectId = rootElement.getAttribute('data-project-id');
    const currentUserId = rootElement.getAttribute('data-current-user-id');
    const isTeamMember = rootElement.getAttribute('data-is-team-member') === 'true';

    if (!rootElement._reactRootContainer) {
        rootElement._reactRootContainer = ReactDOM.createRoot(rootElement);
    }
    
    rootElement._reactRootContainer.render(
        <DiscussionBoard 
            projectId={projectId} 
            currentUserId={currentUserId} 
            isTeamMember={isTeamMember} 
        />
    );
}