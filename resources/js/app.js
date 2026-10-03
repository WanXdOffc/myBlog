import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const article = document.getElementById('article-content');

if (article) {
    initializeReadingProgress();
    initializeTableOfContents(article);
    enhanceArticle(article);
}

function initializeReadingProgress() {
    const progress = document.getElementById('reading-progress');
    const bar = progress?.querySelector('.reading-progress__bar');

    if (!progress || !bar) {
        return;
    }

    let updatePending = false;

    const update = () => {
        const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
        const percentage = scrollableHeight > 0
            ? Math.min(100, Math.max(0, (window.scrollY / scrollableHeight) * 100))
            : 0;

        bar.style.transform = `scaleX(${percentage / 100})`;
        progress.setAttribute('aria-valuenow', String(Math.round(percentage)));
        updatePending = false;
    };

    const scheduleUpdate = () => {
        if (!updatePending) {
            updatePending = true;
            window.requestAnimationFrame(update);
        }
    };

    window.addEventListener('scroll', scheduleUpdate, { passive: true });
    window.addEventListener('resize', scheduleUpdate);
    update();
}

function initializeTableOfContents(articleElement) {
    const toc = document.getElementById('toc-nav');

    if (!toc) {
        return;
    }

    const headings = [...articleElement.querySelectorAll('h2, h3')];
    toc.replaceChildren();

    if (headings.length === 0) {
        const message = document.createElement('p');
        message.className = 'text-xs text-zinc-500 dark:text-zinc-400';
        message.textContent = 'Artikel ini belum memiliki subjudul.';
        toc.append(message);

        return;
    }

    const usedIds = new Set();
    const links = new Map();

    headings.forEach((heading, index) => {
        const baseId = heading.id || heading.textContent
            .trim()
            .toLocaleLowerCase()
            .normalize('NFKD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^\p{L}\p{N}]+/gu, '-')
            .replace(/^-|-$/g, '') || `section-${index + 1}`;
        let id = baseId;
        let suffix = 2;

        while (usedIds.has(id)) {
            id = `${baseId}-${suffix}`;
            suffix += 1;
        }

        usedIds.add(id);
        heading.id = id;

        const link = document.createElement('a');
        link.href = `#${id}`;
        link.textContent = heading.textContent.trim();
        link.className = `toc-link block rounded-sm py-1 text-zinc-600 transition-colors hover:text-zinc-950 focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:text-zinc-400 dark:hover:text-zinc-100 ${
            heading.tagName === 'H3' ? 'pl-3 text-xs' : 'text-sm'
        }`;

        toc.append(link);
        links.set(heading, link);
    });

    let updatePending = false;
    const updateCurrentHeading = () => {
        let currentHeading = headings[0];

        for (const heading of headings) {
            if (heading.getBoundingClientRect().top > 120) {
                break;
            }

            currentHeading = heading;
        }

        for (const [heading, link] of links) {
            if (heading === currentHeading) {
                link.setAttribute('aria-current', 'location');
            } else {
                link.removeAttribute('aria-current');
            }
        }

        updatePending = false;
    };

    const scheduleHeadingUpdate = () => {
        if (!updatePending) {
            updatePending = true;
            window.requestAnimationFrame(updateCurrentHeading);
        }
    };

    window.addEventListener('scroll', scheduleHeadingUpdate, { passive: true });
    window.addEventListener('resize', scheduleHeadingUpdate);
    updateCurrentHeading();
}

async function enhanceArticle(articleElement) {
    try {
        const mathModule = await import('katex/contrib/auto-render');
        const renderMathInElement = mathModule.default;
        renderMathInElement(articleElement, {
            delimiters: [
                { left: '$$', right: '$$', display: true },
                { left: '\\[', right: '\\]', display: true },
                { left: '\\(', right: '\\)', display: false },
                { left: '$', right: '$', display: false },
            ],
            throwOnError: false,
            strict: 'warn',
        });

        const codeBlocks = [...articleElement.querySelectorAll('pre code')];

        if (codeBlocks.length === 0) {
            return;
        }

        const aliases = {
            'c++': 'cpp',
            js: 'javascript',
            py: 'python',
            sh: 'bash',
            shell: 'bash',
            ts: 'typescript',
        };
        const loaders = {
            bash: () => import('shiki/langs/bash.mjs'),
            c: () => import('shiki/langs/c.mjs'),
            cpp: () => import('shiki/langs/cpp.mjs'),
            css: () => import('shiki/langs/css.mjs'),
            html: () => import('shiki/langs/html.mjs'),
            java: () => import('shiki/langs/java.mjs'),
            javascript: () => import('shiki/langs/javascript.mjs'),
            json: () => import('shiki/langs/json.mjs'),
            php: () => import('shiki/langs/php.mjs'),
            python: () => import('shiki/langs/python.mjs'),
            sql: () => import('shiki/langs/sql.mjs'),
            typescript: () => import('shiki/langs/typescript.mjs'),
            xml: () => import('shiki/langs/xml.mjs'),
            yaml: () => import('shiki/langs/yaml.mjs'),
        };
        const blocksToHighlight = [];
        const requestedLanguages = new Set();

        for (const code of codeBlocks) {
            const pre = code.closest('pre');
            const languageClass = [...code.classList].find((name) => name.startsWith('language-'));
            const requestedLanguage = languageClass?.slice('language-'.length);
            const language = aliases[requestedLanguage] ?? requestedLanguage;

            if (!pre || !language || !loaders[language]) {
                if (language) {
                    console.warn(`Bahasa kode "${language}" belum diaktifkan untuk Shiki.`);
                }

                if (pre) {
                    addCopyButton(pre, code);
                }

                continue;
            }

            requestedLanguages.add(language);
            blocksToHighlight.push({ code, language, pre });
        }

        if (blocksToHighlight.length === 0) {
            return;
        }

        const [shikiCore, javascriptEngine] = await Promise.all([
            import('shiki/core'),
            import('shiki/engine/javascript'),
        ]);
        const createHighlighter = shikiCore.createBundledHighlighter({
            langs: loaders,
            themes: {
                'github-dark': () => import('shiki/themes/github-dark.mjs'),
            },
            engine: () => javascriptEngine.createJavaScriptRegexEngine(),
        });
        const highlighter = await createHighlighter({
            themes: ['github-dark'],
            langs: [...requestedLanguages],
        });

        for (const { code, language, pre } of blocksToHighlight) {
            try {
                const highlighted = highlighter.codeToHtml(code.textContent, {
                    lang: language,
                    theme: 'github-dark',
                });
                const template = document.createElement('template');
                template.innerHTML = highlighted;
                const highlightedCode = template.content.querySelector('pre code');

                if (!highlightedCode) {
                    throw new Error(`Shiki tidak menghasilkan markup kode untuk bahasa "${language}".`);
                }

                code.innerHTML = highlightedCode.innerHTML;
                pre.classList.add('shiki');
                pre.style.backgroundColor = '#24292e';
                pre.style.color = '#e1e4e8';
                code.dataset.highlighted = 'true';
            } catch (error) {
                console.error(`Gagal menyorot blok kode "${language}".`, error);
            }

            addCopyButton(pre, code);
        }

        highlighter.dispose();
    } catch (error) {
        console.error('Gagal memuat Shiki atau KaTeX untuk artikel.', error);
    }
}

function addCopyButton(pre, code) {
    if (pre.querySelector('.copy-code-button')) {
        return;
    }

    pre.classList.add('code-block');

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'copy-code-button';
    button.setAttribute('aria-label', 'Salin blok kode');
    button.textContent = 'Salin';

    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(code.textContent);
            button.textContent = 'Tersalin';
        } catch (error) {
            console.error('Tidak dapat menyalin blok kode ke clipboard.', error);
            button.textContent = 'Gagal menyalin';
        }

        window.setTimeout(() => {
            button.textContent = 'Salin';
        }, 2000);
    });

    pre.append(button);
}
