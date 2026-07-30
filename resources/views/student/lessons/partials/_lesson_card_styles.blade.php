<style>
    .lessons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 18px;
    }

    .lesson-card {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .lesson-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow);
    }

    .lesson-video {
        position: relative;
        display: block;
        width: 100%;
        aspect-ratio: 16 / 9;
        background: #000;
        overflow: hidden;
    }

    .lesson-video img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .lesson-play {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, .25);
        color: #fff;
        font-size: 1.6rem;
        opacity: 0;
        transition: .2s;
    }

    .lesson-video:hover .lesson-play {
        opacity: 1;
    }

    .lesson-video-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 2rem;
    }

    .lesson-lock {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: rgba(0, 0, 0, .55);
        color: #FFC24B;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .82rem;
    }

    .lesson-card-body {
        padding: 14px 16px 16px;
    }

    .lesson-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }

    .lesson-card-title {
        font-weight: 700;
        font-size: .96rem;
        line-height: 1.35;
        color: var(--text);
    }

    .save-btn {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 9px;
        border: 1px solid var(--line);
        background: var(--bg-soft);
        color: var(--muted);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: .2s;
    }

    .save-btn:hover {
        color: var(--primary);
        border-color: var(--primary);
    }

    .save-btn.is-saved {
        background: var(--primary-soft);
        color: var(--primary);
        border-color: transparent;
    }

    .lesson-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 8px;
    }

    .lesson-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: .72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .lesson-chip-muted {
        background: var(--bg-soft);
        color: var(--muted);
    }

    .lesson-card-desc {
        font-size: .8rem;
        color: var(--muted);
        line-height: 1.5;
        margin-bottom: 10px;
    }

    .lesson-card-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: .72rem;
        color: var(--muted);
        padding-top: 8px;
        border-top: 1px solid var(--line);
    }

    .lessons-empty {
        background: var(--card);
        border: 1px dashed var(--line);
        border-radius: 18px;
        padding: 56px 24px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .lessons-empty-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    .lessons-empty-title {
        font-weight: 700;
        font-size: 1.05rem;
    }

    .lessons-empty-sub {
        color: var(--muted);
        font-size: .86rem;
        max-width: 360px;
    }

    .lessons-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--mint-soft);
        color: var(--mint);
        border-radius: 12px;
        padding: 13px 16px;
        font-weight: 600;
        font-size: .88rem;
        margin-bottom: 18px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--muted);
        font-weight: 600;
        font-size: .85rem;
        margin-bottom: 16px;
    }

    .back-link:hover {
        color: var(--primary);
    }
</style>
