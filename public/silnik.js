async function login() {
  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value.trim();
  const info = document.getElementById("loginInfo");

  info.innerText = "Logowanie...";

  try {
    const res = await fetch("../api/auth.php", {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify({ username, password })
    });

    const data = await res.json();

    if (!data.success) {
      info.innerText = data.error || "Błąd logowania";
      return;
    }

    info.innerText = `Zalogowano jako ${data.name} (${data.role})`;
    document.getElementById("loginBox").style.display = "none";
    document.getElementById("app").style.display = "block";

    loadTeacherNote();

    if (data.role === "teacher") {
      document.getElementById("teacherText").removeAttribute("readonly");
      document.getElementById("saveTeacherNote").onclick = saveTeacherNote;
    }

  } catch (e) {
    console.error("Błąd fetch:", e);
    info.innerText = "Błąd po stronie serwera.";
  }
}


async function loadTeacherNote() {
  try {
    const res = await fetch("../api/teacher_note.php");
    const data = await res.json();

    document.getElementById("teacherText").value =
      data.content || "Brak notatki.";

  } catch (e) {
    console.error("Błąd fetch:", e);
  }
}


async function saveTeacherNote() {
  const content = document.getElementById("teacherText").value;

  try {
    const res = await fetch("../api/teacher_note.php", {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify({ content })
    });

    const data = await res.json();

    if (data.success) {
      alert("Zapisano notatkę nauczyciela.");
    } else {
      alert("Błąd zapisu.");
    }

  } catch (e) {
    console.error("Błąd fetch:", e);
  }
}
