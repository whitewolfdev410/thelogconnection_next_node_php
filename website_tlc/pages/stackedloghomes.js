import { useRouter } from "next/router";
import { useEffect } from "react";

const RedirectToStackedLogHomePlans = () => {

    const router = useRouter();

    useEffect(() => {
        router.push({ pathname: '/home-plans', query: { style: 'Stacked' }});
    }, []);

    return <></>
}

export default RedirectToStackedLogHomePlans;
