const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;

const theme = localStorage.getItem('theme') || 'light';
if (theme === 'dark') {
    html.classList.add('dark');
}

themeToggle?.addEventListener('click', () => {
    html.classList.toggle('dark');
    const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
    localStorage.setItem('theme', currentTheme);
});
