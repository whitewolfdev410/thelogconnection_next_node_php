import React from "react";
import STYLES from "../../../styles/BuildingStyles.module.scss";
import { SLW_NAV_LIST, SLW_TITLE, SLW_URL_PATH, BUILDING_STYLES_DEFAULT_PATH, BUILDING_STYLES_CONST } from '../../../common/constants/buildingStyles';
import { BannerSection } from '../../../components/common/banner';
import { SubNavbar } from '../../../components/common/subNavbar';
import { NavigatorSection } from '../../../components/common/navigator';
import { SamplePlanComponent } from '../../../components/building-styles/samplePlan';
import { NextPageButton } from '../../../components/common/buttons';
import Head from "next/head";

const IMG_ROOT = `${process.env.IMG_BASE_URL}/building-styles/stacked-log/`;
const IMG_ROOT_BANNER = `${process.env.IMG_BASE_URL}/building-styles/banner/`;

const SLW_SamplePlanPage = () => {

    return (
        <>
            <Head>
                <title>Sample Plan Set - Stacked Logs by The Log Connection</title>
                <meta property="og:title" content={`Sample Plan Set - Stacked Logs by The Log Connection`} />
                <meta property="og:url" content={`${process.env.DOMAIN}/building-styles/stacked-log-walls/sample-plans`} />
                <meta property="og:image" content={`${process.env.DOMAIN}/images/share/The_log_Connection_Logo_Square.jpg`} />
                <meta property="og:description" content={``} />
                <meta property="og:type" content="article" />
            </Head>
            <BannerSection img={`${IMG_ROOT_BANNER}banner.jpg`} />
            <SubNavbar navBarItems={SLW_NAV_LIST} header={BUILDING_STYLES_CONST.SLW_HEADER_LABEL} />
            <NavigatorSection
                hrefPrev={BUILDING_STYLES_DEFAULT_PATH.POST_AND_BEAM_DEFAULT}
                hrefNext={BUILDING_STYLES_DEFAULT_PATH.TIMBER_FRAME_DEFAULT}
                prevLabel={BUILDING_STYLES_CONST.PB_HEADER_LABEL}
                nextLabel={BUILDING_STYLES_CONST.TF_HEADER_LABEL}>
            </NavigatorSection>
            <section className={STYLES.defaultBg}>
                <SamplePlanComponent styleCode="Stacked" />
                <NextPageButton pageUrl={`/home-plans`} pageName={`Go to Home Plans`} />
            </section>
        </>
    )
}

export default SLW_SamplePlanPage;