# Order Completion Proof Policy

BiKuBe currently accepts persisted text completion proof from the assigned worker. Customer acceptance or dispute is recorded separately from order lifecycle, payment, receipt, and payout state.

## Photo Proof Status

Photo proof is deferred. Do not enable photo proof until the media policy and storage protection are accepted.

Before photo proof can be enabled, the implementation must provide:

- customer-safe visibility rules that expose only media attached to the customer's owned order;
- private storage and authorized download routes;
- strict MIME type and file-size validation;
- EXIF and location-metadata removal or an explicitly approved privacy rule;
- malware/virus scanning, or a visible blocker until scanning exists;
- separate worker, customer, support, and admin access boundaries;
- documented retention, deletion, dispute-hold, and privacy-request handling.

Photo evidence must never be public by default, must not expose unrelated people or private locations unnecessarily, and must not be treated as customer confirmation, payment evidence, or payout approval.
