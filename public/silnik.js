const API_BASE = '/k4p/prodzekt/api';

async function login() {
  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;

  try {
    const res = await fetch(`${API_BASE}/auth.php`, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ username, password })
    });

    const data = await res.json();
    if (res.ok) {
      console.log('Zalogowano:', data.user);
      document.getElementById('loginBox').style.display = 'none';
      document.getElementById('app').style.display = 'block';
    } else {
      document.getElementById('loginInfo').innerText = data.error || 'Błąd logowania';
    }
  } catch (err) {
    console.error('Fetch error:', err);
    alert('Nie można połączyć z serwerem.');
  }
}
