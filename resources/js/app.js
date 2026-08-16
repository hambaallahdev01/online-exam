import '../css/icons.css';

import {
    ArrowLeft,
    ArrowRight,
    Bold,
    ChartColumn,
    ChevronDown,
    ChevronUp,
    CircleAlert,
    CircleCheck,
    CircleX,
    Clock,
    createIcons,
    Eraser,
    ExternalLink,
    FileText,
    Flag,
    GraduationCap,
    HandHeart,
    Heart,
    Image,
    Info,
    Italic,
    KeyRound,
    Languages,
    List,
    ListChecks,
    ListOrdered,
    LoaderCircle,
    LogIn,
    Moon,
    Plus,
    Presentation,
    School,
    ShieldCheck,
    SquarePen,
    Strikethrough,
    Sun,
    Trash2,
    TriangleAlert,
    Underline,
    UsersRound,
    Video,
    WandSparkles,
    X,
    Zap,
} from 'lucide';

const icons = {
    ArrowLeft,
    ArrowRight,
    Bold,
    ChartColumn,
    ChevronDown,
    ChevronUp,
    CircleAlert,
    CircleCheck,
    CircleX,
    Clock,
    Eraser,
    ExternalLink,
    FileText,
    Flag,
    GraduationCap,
    HandHeart,
    Heart,
    Image,
    Info,
    Italic,
    KeyRound,
    Languages,
    List,
    ListChecks,
    ListOrdered,
    LoaderCircle,
    LogIn,
    Moon,
    Plus,
    Presentation,
    School,
    ShieldCheck,
    SquarePen,
    Strikethrough,
    Sun,
    Trash2,
    TriangleAlert,
    Underline,
    UsersRound,
    Video,
    WandSparkles,
    X,
    Zap,
};

function renderIcons() {
    createIcons({ icons });

    // Converted SVGs keep data-lucide by default. Removing it prevents later
    // dynamic refreshes from replacing icons that have already been rendered.
    document.querySelectorAll('svg[data-lucide]').forEach((icon) => {
        icon.removeAttribute('data-lucide');
    });
}

let refreshScheduled = false;

function scheduleIconRefresh() {
    if (refreshScheduled) {
        return;
    }

    refreshScheduled = true;
    queueMicrotask(() => {
        refreshScheduled = false;
        renderIcons();
    });
}

function initializeIcons() {
    renderIcons();

    const observer = new MutationObserver((mutations) => {
        const hasNewIcon = mutations.some((mutation) =>
            Array.from(mutation.addedNodes).some((node) =>
                node.nodeType === Node.ELEMENT_NODE
                && (node.matches('[data-lucide]') || node.querySelector('[data-lucide]')),
            ),
        );

        if (hasNewIcon) {
            scheduleIconRefresh();
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
}

window.refreshIcons = scheduleIconRefresh;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeIcons, { once: true });
} else {
    initializeIcons();
}
