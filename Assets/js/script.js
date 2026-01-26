const counters = document.querySelectorAll('[data-count]');

const animate = (el) => {
  const target = Number(el.dataset.count);
  const duration = 900;
  const start = performance.now();

  const step = (now) => {
    const progress = Math.min((now - start) / duration, 1);
    const value = Math.floor(progress * target);
    el.textContent = value.toLocaleString();
    if (progress < 1) requestAnimationFrame(step);
  };

  requestAnimationFrame(step);
};

counters.forEach(animate);
