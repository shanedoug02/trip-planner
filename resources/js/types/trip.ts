export type TripRole = 'owner' | 'member';

export type Trip = {
    id: number;
    name: string;
    destination: string | null;
    departs_on: string; // "2026-12-21"
    created_by: number | null;
    created_at: string;
    updated_at: string;
};

export type TripMember = {
    id: number;
    name: string;
    pivot: { role: TripRole };
};
