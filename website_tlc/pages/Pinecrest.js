import { useRouter } from "next/router";
import { useEffect } from "react";

const HomePlanRedirector = () => {

    const router = useRouter();

const planCode = 'Pinecrest';
    
    const sortBy = 'name';
    const sortDirection = 'asc';

    useEffect(() => {
        router.push({ pathname: '/home-plans/details/floor-plans/' + planCode, query: { sortBy: sortBy, sortDirection: sortDirection, scroll: false } }, undefined, { scroll: true });
    }, []);

    return <></>
}

export default HomePlanRedirector;
