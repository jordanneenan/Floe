(() => {
	const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const videos = [...document.querySelectorAll('.floe-loop')];
	const observer = 'IntersectionObserver' in window ? new IntersectionObserver((entries) => {
		entries.forEach(({ target, isIntersecting }) => {
			if (isIntersecting && !motion.matches) target.play().catch(() => {});
			else target.pause();
		});
	}, { rootMargin: '150px' }) : null;
	videos.forEach((video) => { if (observer) observer.observe(video); else if (!motion.matches) video.play().catch(() => {}); });
	motion.addEventListener('change', () => {
		videos.forEach((video) => { if (motion.matches) video.pause(); else if (!observer) video.play().catch(() => {}); });
	});
	document.addEventListener('click', (event) => {
		const button = event.target.closest('[data-floe-youtube]');
		if (!button) return;
		const id = button.dataset.floeYoutube;
		if (!/^[A-Za-z0-9_-]{11}$/.test(id)) return;
		const cover = button.closest('.floe-video__cover');
		if (!cover) return;
		const iframe = document.createElement('iframe');
		iframe.src = 'https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&rel=0';
		iframe.title = button.getAttribute('aria-label') || 'YouTube video';
		iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
		iframe.allowFullscreen = true;
		cover.replaceChildren(iframe);
		iframe.focus();
	});
})();
