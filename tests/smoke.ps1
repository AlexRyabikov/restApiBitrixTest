param(
  [string]$BaseUrl = "http://localhost:8080/api/notes"
)

$ErrorActionPreference = "Stop"

Write-Host "[1/5] create"
$createBody = @{ title = "Smoke"; content = "Test" } | ConvertTo-Json
$created = Invoke-RestMethod -Method Post -Uri $BaseUrl -ContentType "application/json" -Body $createBody
$id = [int]$created.id
if ($id -le 0) { throw "Failed to parse id from create response" }
$created | ConvertTo-Json -Depth 5

Write-Host "[2/5] list"
Invoke-RestMethod -Method Get -Uri $BaseUrl | ConvertTo-Json -Depth 5

Write-Host "[3/5] view"
Invoke-RestMethod -Method Get -Uri "$BaseUrl/$id" | ConvertTo-Json -Depth 5

Write-Host "[4/5] update"
$updateBody = @{ title = "Smoke updated"; content = "Updated" } | ConvertTo-Json
Invoke-RestMethod -Method Put -Uri "$BaseUrl/$id" -ContentType "application/json" -Body $updateBody | ConvertTo-Json -Depth 5

Write-Host "[5/5] delete"
$deleteResp = Invoke-WebRequest -Method Delete -Uri "$BaseUrl/$id"
Write-Host "HTTP $($deleteResp.StatusCode)"

Write-Host "Smoke test completed"
