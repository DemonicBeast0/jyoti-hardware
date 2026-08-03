document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.navbar');
    const topBtn = document.getElementById('topBtn');

    const updateScrollState = () => {
        const isScrolled = window.scrollY > 80;
        if (navbar) navbar.classList.toggle('scrolled', isScrolled);
        if (topBtn) topBtn.style.display = window.scrollY > 300 ? 'flex' : 'none';
    };

    window.addEventListener('scroll', updateScrollState, { passive: true });
    updateScrollState();

    if (topBtn) {
        topBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    if (typeof Swiper !== 'undefined' && document.querySelector('.heroSwiper')) {
        new Swiper('.heroSwiper', {
            loop: true,
            speed: 1200,
            effect: 'fade',
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: '.heroSwiper .swiper-pagination', clickable: true },
            navigation: { nextEl: '.heroSwiper .swiper-button-next', prevEl: '.heroSwiper .swiper-button-prev' }
        });
    }

    if (typeof Swiper !== 'undefined' && document.querySelector('.testimonialSwiper')) {
        new Swiper('.testimonialSwiper', {
            loop: true,
            spaceBetween: 30,
            autoplay: { delay: 4000, disableOnInteraction: false },
            pagination: { el: '.testimonialSwiper .swiper-pagination', clickable: true },
            breakpoints: { 320: { slidesPerView: 1 }, 768: { slidesPerView: 2 }, 1200: { slidesPerView: 3 } }
        });
    }

    if (typeof AOS !== 'undefined') AOS.init({ duration: 1000, once: true });

    const searchInput = document.getElementById('navbarSearch');
    const suggestions = document.getElementById('searchSuggestions');
    let searchTimer;
    let activeRequest;

    const clearSuggestions = () => {
        if (!suggestions || !searchInput) return;
        suggestions.replaceChildren();
        suggestions.hidden = true;
        searchInput.setAttribute('aria-expanded', 'false');
    };

    const showSuggestions = (products) => {
        if (!suggestions || !searchInput) return;
        suggestions.replaceChildren();
        if (!products.length) {
            const empty = document.createElement('p');
            empty.className = 'm-2 small text-muted';
            empty.textContent = 'No matching products found.';
            suggestions.append(empty);
        } else {
            products.forEach((product) => {
                const link = document.createElement('a');
                link.className = 'search-suggestion';
                link.href = `product-details.php?id=${encodeURIComponent(product.id)}`;
                link.setAttribute('role', 'option');

                const image = document.createElement('img');
                image.src = product.image;
                image.alt = '';

                const details = document.createElement('span');
                const name = document.createElement('strong');
                name.textContent = product.name;
                const brand = document.createElement('small');
                brand.textContent = product.brand_name || '';
                details.append(name, brand);
                link.append(image, details);
                suggestions.append(link);
            });
        }
        suggestions.hidden = false;
        searchInput.setAttribute('aria-expanded', 'true');
    };

    if (searchInput && suggestions) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim();
            window.clearTimeout(searchTimer);
            if (activeRequest) activeRequest.abort();
            if (query.length < 2) {
                clearSuggestions();
                return;
            }
            searchTimer = window.setTimeout(async () => {
                activeRequest = new AbortController();
                try {
                    const response = await fetch(`search-suggestions.php?q=${encodeURIComponent(query)}`, { signal: activeRequest.signal });
                    if (!response.ok) throw new Error('Search request failed');
                    showSuggestions(await response.json());
                } catch (error) {
                    if (error.name !== 'AbortError') clearSuggestions();
                }
            }, 250);
        });
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.navbar-search-wrap')) clearSuggestions();
        });
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') clearSuggestions();
        });
    }
});
