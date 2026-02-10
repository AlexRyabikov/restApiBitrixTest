#!/usr/bin/env sh
set -eu

BASE_URL="${1:-http://localhost:8080/api/notes}"

echo "[1/5] create"
CREATE_RESPONSE=$(curl -sS -X POST "$BASE_URL" \
  -H "Content-Type: application/json" \
  -d '{"title":"Smoke","content":"Test"}')
echo "$CREATE_RESPONSE"

ID=$(echo "$CREATE_RESPONSE" | sed -n 's/.*"id":\([0-9][0-9]*\).*/\1/p')
[ -n "$ID" ] || { echo "Failed to parse id"; exit 1; }

echo "[2/5] list"
curl -sS "$BASE_URL" | head -c 400; echo

echo "[3/5] view"
curl -sS "$BASE_URL/$ID"; echo

echo "[4/5] update"
curl -sS -X PUT "$BASE_URL/$ID" \
  -H "Content-Type: application/json" \
  -d '{"title":"Smoke updated","content":"Updated"}'; echo

echo "[5/5] delete"
curl -sS -o /dev/null -w "HTTP %{http_code}\n" -X DELETE "$BASE_URL/$ID"

echo "Smoke test completed"
