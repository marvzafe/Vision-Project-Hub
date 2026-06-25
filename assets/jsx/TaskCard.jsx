import React from 'react';

export default function TaskCard({ task }) {
    
    const handleDragStart = (e) => {
        // We stringify the data so we can pass multiple pieces of info (like title for the badge)
        const dragData = JSON.stringify({ id: task.id, title: task.title });
        
        e.dataTransfer.setData('application/json', dragData);
        e.dataTransfer.effectAllowed = 'copy'; 
    };

    return (
        <div 
            draggable 
            onDragStart={handleDragStart} 
            className="task-card"
        >
            <h4>{task.title}</h4>
            <p className="text-muted">ID: {task.id}</p>
        </div>
    );
}