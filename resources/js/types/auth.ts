export type User = {
    id: string;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    current_organization_id: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Organization = {
    id: string;
    name: string;
    slug: string;
    created_at: string;
    updated_at: string;
};

export type Auth = {
    user: User;
    currentOrganization: Organization | null;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
