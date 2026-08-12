<style>
    /* ==========================================
       PHASE 2: TOP NAV BAR (MAIN HEADER)
       ========================================== */
    .scrolled-project-title {
        display: inline-block;
        max-width: 0;
        opacity: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-main);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        vertical-align: middle;
    }

    .top-nav-bar.is-scrolled .brand-text,
    .top-nav-bar.is-scrolled .nav-brand-box:hover .brand-text {
        max-width: 0 !important;
        opacity: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .top-nav-bar.is-scrolled .scrolled-project-title {
        max-width: 250px; 
        opacity: 1;
        margin-left: 8px;
        padding-left: 8px;
        border-left: 1px solid var(--border-color);
    }

    .top-nav-bar.is-scrolled .nav-text,
    .top-nav-bar.is-scrolled .profile-info-text,
    .top-nav-bar.is-scrolled .dropdown-icon {
        max-width: 0 !important;
        opacity: 0 !important;
        margin-left: 0 !important;
        padding: 0 !important;
    }

    .top-nav-bar.is-scrolled .nav-item:hover .nav-text,
    .top-nav-bar.is-scrolled .profile-trigger:hover .profile-info-text {
        max-width: 120px !important;
        opacity: 1 !important;
        margin-left: 8px !important;
    }

    .top-nav-bar.is-scrolled .nav-brand-box, 
    .top-nav-bar.is-scrolled .nav-links-box,
    .top-nav-bar.is-scrolled .profile-trigger {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    /* ==========================================
       PHASE 1 -> PHASE 2: PROJECT HEADER
       ========================================== */
    
    /* Inherits styling from .card, only adding sticky layout rules */
    .header {
        position: sticky;
        top: 1rem;
        z-index: 100;
        /* Overrides the default card transition to match our fluid scroll curve */
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    /* Compresses the card into a sleek floating pill when scrolling */
    .header.is-sticky {
        background: rgba(255, 255, 255, 0.85); 
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); 
        padding: 0.85rem 1.5rem; 
    }

    .header.is-sticky .header-meta,
    .header.is-sticky .project-title-text {
        max-height: 0;
        opacity: 0;
        margin: 0;
        padding: 0;
    }

    /* ==========================================
       INDEPENDENT COLUMN SCROLLING (MAXIMIZED)
       ========================================== */
    @media (min-width: 900px) {

        .details-grid {
            align-items: start; 
        }
        
        .left-col, .right-col {
            height: calc(100vh - 120px); 
            display: flex;
            flex-direction: column;
            padding-right: 0; 
            overflow-y: visible; 
        }

        .left-col .card, .right-col .card {
            flex: 1; 
            overflow-y: auto; 
            margin-bottom: 0; 
        }

        .left-col .card::-webkit-scrollbar, .right-col .card::-webkit-scrollbar { width: 6px; }
        .left-col .card::-webkit-scrollbar-track, .right-col .card::-webkit-scrollbar-track { background: transparent; }
        .left-col .card::-webkit-scrollbar-thumb, .right-col .card::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }
        .left-col .card::-webkit-scrollbar-thumb:hover, .right-col .card::-webkit-scrollbar-thumb:hover { 
            background: rgba(0, 0, 0, 0.25); 
        }
    }

    /* ==========================================
       SMART SCROLL HEADER (HIDE/SHOW)
       ========================================== */
    .header-hidden {
        opacity: 0 !important;
        pointer-events: none !important;
    }

    #stickyProjectHeader,
    .project-cover-banner {
        transition: opacity 0.3s ease !important; 
        will-change: opacity; 
    }
</style>