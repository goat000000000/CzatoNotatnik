window.addEventListener("DOMContentLoaded", checkLoginStatus);


async function checkLoginStatus() {
  try {
    const res = await fetch("http://10.103.8.110/k4p/prodzekt/api/auth.php");
    const data = await res.json();

    if (data.logged) {
      document.getElementById("loginBox").style.display = "none";
      document.getElementById("app").style.display = "block";

      loadTeacherNote();

      if (data.user.role === "teacher") {
        document.getElementById("teacherText").removeAttribute("readonly");
        document.getElementById("saveTeacherNote").onclick = saveTeacherNote;
      }
    }
  } catch (e) {
    console.error("Błąd:", e);
  }
}

async function login() {
  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value.trim();
  const info = document.getElementById("loginInfo");

  info.innerText = "Logowanie...";

  try {
    const res = await fetch("http://10.103.8.110/k4p/prodzekt/api/auth.php", {
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
    console.error("Błąd:", e);
    info.innerText = "Błąd po stronie serwera.";
  }
}

async function loadTeacherNote() {
  try {
    const res = await fetch("http://10.103.8.110/k4p/prodzekt/api/teacher_note.php");
    const data = await res.json();
    document.getElementById("teacherText").value = data.content || "";
  } catch (e) {
    console.error("Błąd:", e);
  }
}

async function saveTeacherNote() {
  const content = document.getElementById("teacherText").value;

  try {
    const res = await fetch("http://10.103.8.110/k4p/prodzekt/api/teacher_note.php", {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify({ content })
    });

    const data = await res.json();

    if (data.success) {
      alert("Zapisano notatkę.");
    } else {
      alert("Błąd zapisu.");
    }
  } catch (e) {
    console.error("Błąd:", e);
  }
}
async function sendMsg() {
  const input = document.getElementById("msgInput");
  const text = input.value.trim();

  if (!text) return;

  try {
      const res = await fetch("http://10.103.8.110/k4p/prodzekt/api/chat_send.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "same-origin",
          body: JSON.stringify({ text })
      });

      if (!res.ok) throw new Error(res.status + " " + res.statusText);

      input.value = "";
      loadMessages(); 
  } catch (e) {
      console.error("Błąd sendMsg:", e);
  }
}

document.getElementById("sendMsg").addEventListener("click", sendMsg);
document.getElementById("msgInput").addEventListener("keypress", e => {
  if (e.key === "Enter") sendMsg();
});

async function loadMessages() {
  try {
      const res = await fetch("http://10.103.8.110/k4p/prodzekt/api/chat_get.php", {
          method: "GET",
          credentials: "same-origin"
      });

      if (!res.ok) throw new Error(res.status + " " + res.statusText);

      const data = await res.json();
      if (!data.success) return;

      const box = document.getElementById("messages");
      box.innerHTML = "";

      data.messages.reverse().forEach(msg => {
          const div = document.createElement("div");
          div.className = "msg";
          div.innerHTML = `<b>${msg.username}</b>: ${msg.text}`;
          box.appendChild(div);
      });

      box.scrollTop = box.scrollHeight;
  } catch (e) {
      console.error("Błąd loadMessages:", e);
  }
}

setInterval(() => {
  if (document.getElementById("app").style.display !== "none") {
      loadMessages();
  }
}, 2000);
