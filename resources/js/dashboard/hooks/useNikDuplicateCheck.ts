import { useState, useEffect, useRef, useCallback } from 'react';

export interface DuplicateEntry {
    institution_id: string;
    person_id: string;
    institution_name: string;
}

export function useNikDuplicateCheck() {
    const [duplicates, setDuplicates] = useState<DuplicateEntry[]>([]);
    const [loading, setLoading] = useState(false);
    const timerRef = useRef<ReturnType<typeof setTimeout>>();

    const check = useCallback((nik: string) => {
        if (!nik || nik.length < 16) {
            setDuplicates([]);
            return;
        }

        if (timerRef.current) clearTimeout(timerRef.current);

        timerRef.current = setTimeout(async () => {
            setLoading(true);
            try {
                const res = await fetch(`/persons/check-nik/${encodeURIComponent(nik)}`);
                const data = await res.json();
                setDuplicates(data.duplicates || []);
            } catch {
                setDuplicates([]);
            } finally {
                setLoading(false);
            }
        }, 500);
    }, []);

    useEffect(() => {
        return () => { if (timerRef.current) clearTimeout(timerRef.current); };
    }, []);

    const reset = () => { setDuplicates([]); setLoading(false); };

    return { duplicates, loading, check, reset };
}
