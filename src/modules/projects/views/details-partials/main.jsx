// 1. Remove imports:
// import React from 'react';
// import { createRoot } from 'react-dom/client';
// import DiscussionBoard from './DiscussionBoard';

document.addEventListener('DOMContentLoaded', () => {
    const rootElement = document.getElementById('react-discussion-root');

    if (rootElement) {
        const projectId = rootElement.getAttribute('data-project-id');
        const currentUserId = rootElement.getAttribute('data-current-user-id');
        const isTeamMember = rootElement.getAttribute('data-is-team-member') === 'true';

        // 2. Use the global ReactDOM object
        const root = ReactDOM.createRoot(rootElement);
        root.render(
            <DiscussionBoard 
                projectId={projectId} 
                currentUserId={currentUserId} 
                isTeamMember={isTeamMember} 
            />
        );
    }
});