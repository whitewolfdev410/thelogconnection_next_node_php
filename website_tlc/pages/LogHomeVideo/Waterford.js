import { useRouter } from "next/router";
import { useEffect } from "react";

const HomePlanVideoRedirector = () => {

    const router = useRouter();

const planCode = 'Waterford';
    

    const sortBy = 'name';
    const sortDirection = 'asc';

    useEffect(() => {
        router.push({ pathname: '/home-plans/details/image-gallery/' + planCode, query: { sortBy: sortBy, sortDirection: sortDirection, scroll: false } }, undefined, { scroll: true });
    }, []);

    return <></>
}

export default HomePlanVideoRedirector;
