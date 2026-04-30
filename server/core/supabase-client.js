// src/core/supabaseClient.js
import { createClient } from '@supabase/supabase-js';

// In React (Vite), environment variables start with VITE_ and are safe for the browser
const supabaseUrl = import.meta.env.VITE_SUPABASE_URL;
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON_KEY;

export const supabase = createClient(supabaseUrl, supabaseAnonKey);