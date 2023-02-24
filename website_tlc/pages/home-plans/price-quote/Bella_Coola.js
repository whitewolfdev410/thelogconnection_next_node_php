import React, { useEffect, useState } from "react";
import { useRouter } from "next/router";
import { SubNavbar } from '../../../components/common/subNavbar';
import { PriceQuoteSection } from '../../../components/home-plans/priceQuoteSection';
import { getHomePlan } from "../../../common/services/home-plans";
import { processHomePlans } from "../../../components/home-plans/homePlanHelper";
import { ScrollIntoView } from "../../../components/common/scrollIntoView";
import { FloaterNav } from "../../../components/home-plans/floaterNav";
import { HomePlanBanner } from "../../../components/home-plans/homePlanBanner";
import { HOME_PLAN_DETAILS_PATH } from '../../../common/constants/homePlans';
import Head from "next/head";

const IMG_BANNER_FILE_ROOT = `${process.env.IMG_BASE_URL}/home-plans/_banner/`;

const PriceQuotePage = () => {

    const router = useRouter();
const planCode = 'Bella_Coola';
    

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
                <title>{`${planCode}`} Log Home Design Price Quotation</title>
                <meta property="og:image" content={`${IMG_BANNER_FILE_ROOT}${planCode}.jpg`} />
                <meta property="og:type" content="article" />
                <meta property="og:url" content={`${HOME_PLAN_DETAILS_PATH.PRICE_QUOTE}/${planCode}`} />
                <meta property="og:title" content={`${planCode} Log Home Design Price Quotation`} />
                <meta property="og:description" content={`Receive price quotation for ${planCode}`} />
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
