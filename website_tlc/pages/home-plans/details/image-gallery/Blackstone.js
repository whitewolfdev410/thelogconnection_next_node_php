import React, { useEffect, useState } from "react";
import { useRouter } from "next/router";
import { SubNavbar } from '../../../../components/common/subNavbar';
import { ImageGallerySection } from '../../../../components/home-plans/imageGallerySection';
import { getHomePlan } from "../../../../common/services/home-plans";
import { processHomePlans } from "../../../../components/home-plans/homePlanHelper";
import { NavigatorSection } from '../../../../components/common/navigator';
import { HomePlanNav } from "../../../../components/home-plans/homePlanNav"
import { ScrollIntoView } from "../../../../components/common/scrollIntoView";
import { FloaterNav } from "../../../../components/home-plans/floaterNav";
import { HomePlanBanner } from "../../../../components/home-plans/homePlanBanner";
import { HOME_PLAN_DETAILS_PATH } from '../../../../common/constants/homePlans';
import Head from "next/head";

const IMG_BANNER_FILE_ROOT = `${process.env.IMG_BASE_URL}/home-plans/_banner/`;

const ImageGalleryPage = () => {

    const router = useRouter();
const planCode = 'Blackstone';
    
    let sortBy = router.query.sortBy;
    let sortDirection = router.query.sortDirection;

    const [header, setHeader] = useState('');
    const [navBarItems, setNavBarItems] = useState([]);
    const [nextPlanLink, setNextPlanLink] = useState('');
    const [currHomePlan, setCurrHomePlan] = useState({});
    const [allHomePlans, setAllHomePlans] = useState([]);
    const [processedHomePlans, setProcessedHomePlans] = useState([]);

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
                    let hp = processHomePlans(planCode, homePlans, 'gallery');
                    setCurrHomePlan(hp.currHomePlan);
                    setHeader(hp.header);
                    setNavBarItems(hp.navBarItems);

                    if (!hp.nextHomePlan) {
                        hp.nextHomePlan = homePlans[0];
                    }

                    setProcessedHomePlans({ ...hp });

                    if (hp.nextHomePlan) {
                        setNextPlanLink(`${HOME_PLAN_DETAILS_PATH.FLOOR_PLAN}/${hp.nextHomePlan.planCode}?scroll=false&sortBy=${sortBy}&sortDirection=${sortDirection}`)
                    }
                }

            }).catch((err) => {
                console.log(err);
            });
    }, [planCode, sortBy, sortDirection]);

    return (
        <>
            <Head>
                <title>{`${planCode}`} Log Home Design Gallery</title>
                <meta property="og:image" content={`${IMG_BANNER_FILE_ROOT}${planCode}.jpg`} />
                <meta property="og:type" content="article" />
                <meta property="og:url" content={`${HOME_PLAN_DETAILS_PATH.IMAGE_GALLERY}/${planCode}`} />
                <meta property="og:title" content={`${planCode} Log Home Design Gallery`} />
                <meta property="og:description" content={`Gallery for ${planCode}`} />
            </Head>
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
            <FloaterNav homePlans={allHomePlans} context="image-gallery" />
            <ImageGallerySection homePlan={currHomePlan} />
            <HomePlanNav data={processedHomePlans} page={'IMAGE_GALLERY'} />
        </>
    );
}

export default ImageGalleryPage;
