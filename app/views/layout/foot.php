<script>
  const toggle = document.getElementById('themeToggle');

  // restore saved choice
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark');
    toggle.textContent = '☀️';
  }

  toggle.addEventListener('click', () => {
    const isDark = document.body.classList.toggle('dark');
    toggle.textContent = isDark ? '☀️' : '🌙';
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  });
</script>

</body>
</html>