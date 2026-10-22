<?php

// --- Passwords for accounts the site creates itself; the values live in the environment (Vercel dashboard), never in the code ---

return [

    // --- Starting password of imported accounts. It never works for a password login: people sign in with Google first and choose their own password. Empty: every imported account gets a random one ---
    'starting_password' => env('IMPORT_STARTING_PASSWORD'),

    // --- Password of the sample accounts from the seeders (local development). Empty: a random one ---
    'seed_password' => env('SEED_PASSWORD'),

];
