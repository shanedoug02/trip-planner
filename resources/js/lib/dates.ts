/**
 * Format a date-only string like "2026-12-21" as "Mon, Dec 21, 2026".
 *
 * `new Date("2026-12-21")` would read it as midnight UTC, which is the evening
 * of Dec 20 in Guatemala. Building the date from its parts keeps it local.
 */
export function formatDate(value: string): string {
    const [year, month, day] = value.split('-').map(Number);

    return new Date(year, month - 1, day).toLocaleDateString(undefined, {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}
