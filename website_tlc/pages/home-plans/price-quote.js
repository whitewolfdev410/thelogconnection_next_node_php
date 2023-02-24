import React, { useEffect, useState } from "react";
import { useRouter } from "next/router";
import { SubNavbar } from '../../components/common/subNavbar';
import { PriceQuoteSection } from '../../components/home-plans/priceQuoteSection';
import { getHomePlan } from "../../common/services/home-plans";
import { processHomePlans } from "../../components/home-plans/homePlanHelper";
import { ScrollIntoView } from "../../components/common/scrollIntoView";
import { FloaterNav } from "../../components/home-plans/floaterNav";
import { HomePlanBanner } from "../../components/home-plans/homePlanBanner";
import Head from "next/head";

const IMG_BANNER_FILE_ROOT = `${process.env.IMG_BASE_URL}/home-plans/_banner/`;

const PriceQuotePage = () => {

    const router = useRouter();
    let planCode = router.query.plan;

    const [header, setHeader] = useState("");
    const [navBarItems, setNavBarItems] = useState([]);
    const [allHomePlans, setAllHomePlans] = useState([]);
    const [processedHomePlans, setProcessedHomePlans] = useState([]);

    useEffect(() => {
        getHomePlan(planCode).then((homePlan) => {
            let hp = processHomePlans(planCode, homePlan, 'priceQuote');
            setHeader(hp.header);
            setNavBarItems(hp.navBarItems);
        });
    }, [planCode]);

    useEffect(() => {
        setProcessedHomePlans({});

        getHomePlan('all').then((homePlans) => {
            if (homePlans) {
                setAllHomePlans(homePlans);
                let hp = processHomePlans(planCode, homePlans);

                if (!hp.nextHomePlan) {
                    hp.nextHomePlan = homePlans[0];
                }

                setProcessedHomePlans({ ...hp });
            }
        }).catch((err) => {

        });
    }, []);

    return (
        <>
            <Head>
                <title>{Object.keys(processedHomePlans).length > 0 && processedHomePlans.currHomePlan && processedHomePlans.currHomePlan.name} Log Home Design</title>
            </Head>
            <HomePlanBanner
                img={`${IMG_BANNER_FILE_ROOT}${planCode}.jpg`}
                data={{ ...processedHomePlans }}
            />
            <ScrollIntoView mode={'default'} />
            <SubNavbar navBarItems={navBarItems} header={header} />
            <FloaterNav homePlans={allHomePlans} context="price-quote" />
            <PriceQuoteSection planCode={planCode} />
        </>
    );
}

export default PriceQuotePage;