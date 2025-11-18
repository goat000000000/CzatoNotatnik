const API_BASE = '/prodzekt/CzatoNotatnik/api';

let currentUser = null;

// -------------------- LOGIN --------------------
async function login() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    try {
        const res = await fetch(`${API_BASE}/auth.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const data = await res.json();

        if (res.ok) {
            currentUser = data.user; // zapisujemy dane użytkownika
            console.log('Zalogowano:', currentUser);
            alert("API returned: " + JSON.stringify(data));

            // pokaż aplikację
            document.getElementById('loginBox').style.display = 'none';
            document.getElementById('app').style.display = 'block';

            // ustaw uprawnienia
            enforcePermissions();

            // pobierz notatkę nauczyciela po zalogowaniu
            loadTeacherNote();

        } else {
            document.getElementById('loginInfo').innerText = data.error || 'Błąd logowania';
        }

    } catch (err) {
        console.error('Fetch error:', err);
        alert('Nie można połączyć z serwerem.');
    }
}

// -------------------- USTAWIENIE UPRAWNIEŃ --------------------
function enforcePermissions() {
    const teacherArea = document.getElementById('teacherText');

    if (!currentUser || !teacherArea) return;

    if (currentUser.role === 'student') {
        teacherArea.readOnly = true;
        teacherArea.style.backgroundColor = "#eee";
        console.log("STUDENT → tylko podgląd");
    } else if (currentUser.role === 'teacher') {
        teacherArea.readOnly = false;
        teacherArea.style.backgroundColor = "#fff";
        console.log("TEACHER → edycja włączona");
    }
}

// -------------------- POBIERANIE NOTATKI NAUCZYCIELA --------------------
async function loadTeacherNote() {
    try {
        const res = await fetch(`${API_BASE}/teacher_note.php`);
        const data = await res.json();

        const teacherArea = document.getElementById('teacherText');
        if (teacherArea) {
            teacherArea.value = data.note || '';
            enforcePermissions(); // zawsze pilnujemy roli po załadowaniu
        }

    } catch (err) {
        console.error('Błąd pobierania notatki nauczyciela:', err);
    }
}

// -------------------- ZAPIS NOTATKI UCZNIA --------------------
document.getElementById('saveNote')?.addEventListener('click', async () => {
    const myText = document.getElementById('myText').value;

    try {
        const res = await fetch(`${API_BASE}/save_student_note.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: currentUser.username, note: myText })
        });

        const data = await res.json();
        if (res.ok) {
            alert('Notatka zapisana!');
        } else {
            alert('Błąd zapisu: ' + (data.error || ''));
        }

    } catch (err) {
        console.error('Błąd zapisu notatki:', err);
        alert('Nie można połączyć z serwerem.');
    }
});

// -------------------- ZAPIS NOTATKI NAUCZYCIELA (TYLKO TEACHER) --------------------
document.getElementById('teacherText')?.addEventListener('input', async () => {
    if (!currentUser || currentUser.role !== 'teacher') return;

    const note = document.getElementById('teacherText').value;

    try {
        await fetch(`${API_BASE}/save_teacher_note.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ note })
        });
        console.log('Notatka nauczyciela zapisana');
    } catch (err) {
        console.error('Błąd zapisu notatki nauczyciela:', err);
    }
});
  