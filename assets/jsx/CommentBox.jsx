import React, { useState } from 'react';

export default function CommentBox({ projectId, onCommentAdded }) {
    const [content, setContent] = useState('');
    const [attachedTask, setAttachedTask] = useState(null);
    const [isDraggingOver, setIsDraggingOver] = useState(false);
    const [isSubmitting, setIsSubmitting] = useState(false);

    // 1. You MUST prevent the default behavior, otherwise the browser won't allow the drop
    const handleDragOver = (e) => {
        e.preventDefault(); 
        if (!isDraggingOver) setIsDraggingOver(true);
    };

    const handleDragLeave = () => {
        setIsDraggingOver(false);
    };

    // 2. Handle the actual drop
    const handleDrop = (e) => {
        e.preventDefault();
        setIsDraggingOver(false);

        // Retrieve and parse the data we set in TaskCard
        const droppedData = e.dataTransfer.getData('application/json');
        
        if (droppedData) {
            try {
                const task = JSON.parse(droppedData);
                setAttachedTask(task);
            } catch (err) {
                console.error("Failed to parse dropped task data", err);
            }
        }
    };

    // 3. Submit to your PHP Controller
    const submitComment = async () => {
        // Validate
        if (!content.trim() && !attachedTask) {
            alert("Please enter a comment or attach a task.");
            return;
        }

        setIsSubmitting(true);

        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('project_id', projectId);
        formData.append('content', content);
        
        if (attachedTask) {
            formData.append('task_id', attachedTask.id);
        }

        try {
            const response = await fetch('/src/modules/discussions/discussion-controller.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Reset state
                setContent('');
                setAttachedTask(null);
                // Trigger a refresh of the discussion thread if needed
                if (onCommentAdded) onCommentAdded(); 
            } else {
                alert(result.message || "Failed to add comment.");
            }
        } catch (error) {
            console.error("Error submitting comment:", error);
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div 
            onDrop={handleDrop} 
            onDragOver={handleDragOver} 
            onDragLeave={handleDragLeave}
            className={`comment-box ${isDraggingOver ? 'drag-over' : ''}`}
        >
            <textarea 
                value={content} 
                onChange={(e) => setContent(e.target.value)}
                placeholder="Type a comment or drop a task here..."
                rows="4"
                style={{ width: '100%' }}
            />
            
            {/* Visual indicator that a task is attached */}
            {attachedTask && (
                <div className="task-badge">
                    Attached Task: <strong>{attachedTask.title}</strong>
                    <button type="button" onClick={() => setAttachedTask(null)}>
                        &times; Remove
                    </button>
                </div>
            )}
            
            <div style={{ marginTop: '10px' }}>
                <button 
                    onClick={submitComment} 
                    disabled={isSubmitting || (!content.trim() && !attachedTask)}
                >
                    {isSubmitting ? 'Sending...' : 'Send Comment'}
                </button>
            </div>
        </div>
    );
}