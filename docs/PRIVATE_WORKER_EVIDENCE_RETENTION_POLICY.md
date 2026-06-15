# Private Worker Evidence Retention Policy

Worker payout evidence exists only to support identity, tax, and payout-compliance review. Files remain on private storage and never receive a public URL.

## Security Gate

- Evidence is PDF-only until an accepted image EXIF-removal policy exists.
- Every upload receives a SHA-256 hash and a malware scan status.
- Reviewer download is blocked unless the scan verdict is `clean`.
- Owner/admin override requires a dedicated permission, explicit reason, and audit event.
- Malware scanning is fail-closed. When no scanner is available, evidence remains `scan_unavailable`.
- Upload and scan results never approve a review automatically.

## Retention

- Keep evidence while its review is active.
- Keep evidence while related settlement and payout audit obligations remain active.
- Physical deletion requires a separately approved retention job and authorization workflow.
- Retention scheduling and deletion must be audited.

## Access

- The worker may access only their own evidence.
- Authorized reviewers may access evidence only for their permitted review type.
- Customers and unrelated workers have no access.
