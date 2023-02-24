import { MDBContainer, MDBRow, MDBCol } from "mdbreact";
import React from "react";
import LS_STYLES from "../../../styles/building-styles/LogSpecies.module.scss";
import { PB_NAV_LIST, PB_TITLE, BUILDING_STYLES_DEFAULT_PATH, BUILDING_STYLES_CONST, PB_URL_PATH } from '../../../common/constants/buildingStyles';
import { BannerSection } from '../../../components/common/banner';
import { SubNavbar } from '../../../components/common/subNavbar';
import { NavigatorSection } from '../../../components/common/navigator';
import { LogSpeciesModal } from '../../../components/building-styles/logSpecies';
import { NextPageButton } from '../../../components/common/buttons';
import Head from "next/head";

const IMG_ROOT_BANNER = `${process.env.IMG_BASE_URL}/building-styles/banner/`;

const PB_LogSpeciesPage = () => {

    return (
        <>
            <Head>
                <title>Wood Species Use in Post &amp; Beam Log Homes</title>
                <meta property="og:title" content={`Wood Species Use in Post & Beam Log Homes`} />
                <meta property="og:url" content={`${process.env.DOMAIN}/building-styles/post-and-beam/log-species`} />
                <meta property="og:image" content={`${process.env.DOMAIN}/images/share/PB_LogSpecies_Share.jpg`} />
                <meta property="og:type" content="article" />
                <meta property="og:description" content={``} />
            </Head>
            <BannerSection img={`${IMG_ROOT_BANNER}banner.jpg`} />
            <SubNavbar navBarItems={PB_NAV_LIST} header={BUILDING_STYLES_CONST.PB_HEADER_LABEL} />
            <NavigatorSection
                hrefPrev={BUILDING_STYLES_DEFAULT_PATH.TIMBER_FRAME_DEFAULT}
                hrefNext={BUILDING_STYLES_DEFAULT_PATH.STACKED_LOG_WALLS_DEFAULT}
                prevLabel={BUILDING_STYLES_CONST.TF_HEADER_LABEL}
                nextLabel={BUILDING_STYLES_CONST.SLW_HEADER_LABEL}>
            </NavigatorSection>

            <section className={LS_STYLES.logSpecies}>
                <MDBContainer className={LS_STYLES.section1}>
                    <MDBRow>
                        <MDBCol md="6" sm="12" className={LS_STYLES.leftCont}>
                            <div className={LS_STYLES.mainImgCont}><img className="disablecopy" src='/images/building-styles/post-and-beam/log-species/pb_ls_1.jpg' /></div>
                        </MDBCol>
                        <MDBCol md="6" sm="12" className={LS_STYLES.rightCont}>
                            <h3>Post and Beam Log Species</h3>
                            <p>Our location in the interior of British Columbia, Canada provides a selection of home building logs unsurpassed worldwide. Only premium quality logs are hand selected from trees growing at high elevations. These trees experience slower growth rates, resulting in a higher structural value due to tighter annual growth rings. In addition to strength, each log is hand selected based on straightness of grain and consistency of size.</p>
                        </MDBCol>
                    </MDBRow>
                </MDBContainer>
                <MDBContainer>
                    <MDBRow center className={LS_STYLES.section2} >
                        <MDBCol md="6" sm="12">
                            <div className={LS_STYLES.card}>
                                <img className="disablecopy" src='/images/building-styles/post-and-beam/log-species/pb_ls_card_douglasFir.jpg' />
                                <h4>Douglas Fir</h4>
                                <p>Douglas Fir is a very strong wood with a rich reddish heartwood. Generally tight growth rings, straight grain, medium heavy with good resistance to mildew and fungus.</p>
                                <p>Due to its high strength, Fir is a perfect choice for both wall logs and beams and joists. Its weight may make transportation costs slightly higher.</p>
                                <LogSpeciesModal logSpecie={'DF'} />
                            </div>
                        </MDBCol>
                        <MDBCol md="6" sm="12">
                            <div className={LS_STYLES.card}>
                                <img className="disablecopy" src='/images/building-styles/post-and-beam/log-species/pb_ls_card_westernRedCedar.jpg' />
                                <h4>Western Red Cedar</h4>
                                <p>Western Red Cedar has a reddish to dull brown heartwood. It is very resistant to decay, and features generally straight grains with uniform texture. Other advantages are low shrinkage and light weight. However cedar is moderately soft, and provides low strength when used as beams.</p>
                                <p>Cedar can be used for most components in post and beam homes if the logs are oversized large enough. Also, cedar is very popular for use as character posts.</p>
                                <LogSpeciesModal logSpecie={'WRC'} />
                            </div>
                        </MDBCol>
                    </MDBRow>
                </MDBContainer>
                <NextPageButton pageUrl={PB_URL_PATH.DAS} pageName={PB_TITLE.DAS} />
            </section>
        </>
    )

}

export default PB_LogSpeciesPage;