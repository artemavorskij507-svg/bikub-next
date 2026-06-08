# BiKuBe Next Porting Rules

Status: guardrail document for migration from old BiKuBe.

## Old Project Boundary

Old project path:

`/home/keks/bikube`

New project path:

`/home/keks/bikube-next`

The old project is source material only. Do not mutate it while building BiKuBe Next.

## Port Later

Port only proven domain knowledge:

- Order Engine concepts.
- Lifecycle rules.
- Service scenario lessons.
- Dispatch assignment lessons.
- Courier/GPS visibility rules.
- Payment event and idempotency lessons.
- Support/claim flow lessons.
- Compliance boundaries for Narvik/Ballangen launch.

## Do Not Port

- Old Filament navigation chaos.
- Debug/demo pages.
- Broken hubs.
- Fake actions.
- Fake GPS/maps/routes/payments.
- Bad Blade UI.
- Duplicate resources.
- Social-care launch surfaces without legal review.
- Any module that cannot explain its business purpose.

## Migration Discipline

1. Define target bounded context first.
2. Inspect old code for reusable logic.
3. Extract behavior into a clean service or domain model.
4. Write tests or evidence commands before wiring UI.
5. Keep UI actions honest: working, disabled with reason, removed, or approval-required.
6. Never connect BiKuBe Next to old production DB until an explicit migration plan is approved.

## Immediate Next Porting Candidate

The first candidate should be a small domain model contract, not UI:

- Service Scenario registry.
- Order lifecycle state machine.
- Worker GPS presence contract.

This keeps the new project from becoming a copied admin shell.
