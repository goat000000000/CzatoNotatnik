# API - dokumentacja

Base path: /api

## POST /auth.php
Body: { username, password }
Response: { ok: true, user: {id, name, role} }

## GET /auth.php?action=logout

## GET /messages.php?last_id=X
Response: { messages: [ {id, user_id, name, text, created_at}, ... ] }

## POST /messages.php
Body: { text }
Auth: wymagana

## GET /board.php
Response: { board: { content, updated_at } }

## POST /board.php
Body: { content }
Auth: teacher only

## GET /notes.php
Auth: required
Response: { note: { id, content, updated_at } }

## POST /notes.php
Body: { content }
Auth: required
