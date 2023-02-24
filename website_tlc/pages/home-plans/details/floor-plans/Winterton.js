import React, { useEffect, useState } from "react";
import { useRouter } from "next/router";
import { SubNavbar } from '../../../../components/common/subNavbar';
import { FloorPlanSection } from '../../../../components/home-plans/floorPlanSection';
import { HomePlanNav } from "../../../../components/home-plans/homePlanNav"
import { HOME_PLAN_DETAILS_PATH } from '../../../../common/constants/homePlans';
import { getHomePlan } from "../../../../common/services/home-plans";
import { processHomePlans } from "../../../../components/home-plans/homePlanHelper";
import { NavigatorSection } from '../../../../components/common/navigator';
import { ScrollIntoView } from "../../../../components/common/scrollIntoView";
import { FloaterNav } from "../../../../components/home-plans/floaterNav";
import { HomePlanBanner } from "../../../../components/home-plans/homePlanBanner";
import Head from "next/head";

const IMG_BANNER_FILE_ROOT = `${process.env.IMG_BASE_URL}/home-plans/_banner/`;

const FloorPlanPage = () => {

    const [floorPlans, setFloorPlans] = useState([]);
    const [header, setHeader] = useState('');
    const [navBarItems, setNavBarItems] = useState([]);
    const [processedHomePlans, setProcessedHomePlans] = useState({});
    const [nextPlanLink, setNextPlanLink] = useState('');
    const [allHomePlans, setAllHomePlans] = useState([]);

    const router = useRouter();
    let planCode = 'Winterton';
    let sortBy = router.query.sortBy;
    let sortDirection = router.query.sortDirection;

    useEffect(() => {
        setProcessedHomePlans({});

        let filterParameters = {
            sort_by: sortBy,
            sort_direction: sortDirection
        };

        getHomePlan('all', filterParameters)
            .then((homePlans) => {
                if (homePlans) {
                    setAllHomePlans(homePlans);
                    let hp = processHomePlans(planCode, homePlans);
                    setFloorPlans(hp.currHomePlan.floorPlans);
                    setHeader(hp.header);
                    setNavBarItems(hp.navBarItems);

                    if (!hp.nextHomePlan){
                        hp.nextHomePlan = homePlans[0];
                    }

                    setProcessedHomePlans({ ...hp });

                    if (hp.nextHomePlan) {
                        setNextPlanLink(`${HOME_PLAN_DETAILS_PATH.FLOOR_PLAN}/${hp.nextHomePlan.planCode}?scroll=false&sortBy=${sortBy}&sortDirection=${sortDirection}`);
                    }
                }

            });
    }, [planCode, sortBy, sortDirection]);

    return (
        <>
            <Head>
                <title>{`Winterton`} Log Home Design by The Log Connection</title>
                <meta property="og:image" content={`${IMG_BANNER_FILE_ROOT}Winterton.jpg`} />
                <meta property="og:type" content="article" />
                <meta property="og:url" content={`${HOME_PLAN_DETAILS_PATH.FLOOR_PLAN}/Winterton?scroll=false`} />
                <meta property="og:title" content={`Winterton Log Home Design by The Log Connection`} />
                <meta property="og:description" content={`Floor plans for Winterton`} />
            </Head>
            <section>
                <HomePlanBanner
                    img={`${IMG_BANNER_FILE_ROOT}${planCode}.jpg`}
                    data={{ ...processedHomePlans }}
                />
                <ScrollIntoView mode={'default'} />
                <SubNavbar navBarItems={navBarItems} header={header} />
                <NavigatorSection
                    bgColor="white"
                    hrefPrev={`/home-plans`}
                    hrefNext={nextPlanLink}
                    prevLabel={`Back to Gallery`}
                    nextLabel={nextPlanLink ? `Next Plan` : ''}
                />
                <FloaterNav homePlans={allHomePlans} context="floor-plans" />
                <FloorPlanSection floorPlans={floorPlans} />
                <HomePlanNav data={processedHomePlans} page={'FLOOR_PLAN'} />
            </section>
        </>
    );
}

export default FloorPlanPage;