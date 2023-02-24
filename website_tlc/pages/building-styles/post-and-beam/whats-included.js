import { MDBContainer, MDBRow, MDBCol } from "mdbreact";
import React, { } from "react";
import STYLES from "../../../styles/building-styles/WhatsIncluded.module.scss";
import { PB_NAV_LIST, PB_TITLE, BUILDING_STYLES_DEFAULT_PATH, BUILDING_STYLES_CONST, PB_URL_PATH } from '../../../common/constants/buildingStyles';
import { BannerSection } from '../../../components/common/banner';
import { SubNavbar } from '../../../components/common/subNavbar';
import { NavigatorSection } from '../../../components/common/navigator';
import { NextPageButton } from '../../../components/common/buttons';
import { WiComponent } from '../../../components/building-styles/whatsIncluded';
import Head from "next/head";

const IMG_ROOT_BANNER = `${process.env.IMG_BASE_URL}/building-styles/banner/`;

const PB_WhatsIncludedPage = () => {

    return (
        <>
            <Head>
                <title>What's Included in Post &amp; Beam Log Home Package</title>
                <meta property="og:title" content={`What's Included in Post & Beam Log Home Package`} />
                <meta property="og:url" content={`${process.env.DOMAIN}/building-styles/post-and-beam/whats-included`} />
                <meta property="og:image" content={`${process.env.DOMAIN}/images/share/PB_WhatsIncluded_Share.jpg`} />
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
            <section className={STYLES.whatsIncluded}>
                <MDBContainer >
                    <MDBRow className={STYLES.section1} center>
                        <MDBCol md="6" className={STYLES.mainLeft}>
                            <MDBRow>
                                <MDBCol md="12" className={STYLES.mainTitleCont}><h3>WHAT'S INCLUDED IN A POST AND BEAM LOG SHELL PACKAGE</h3></MDBCol>
                                <MDBCol md="12" className={STYLES.mainDescription}>
                                    <p>A post and beam home package from The Log Connection includes the complete structural log frame and all the components necessary to assemble and lock the package in place upon the delivery to your building site.</p>
                                    <p>Our highlight list below is generic to all our post and beam packages. Your package may include additional items specific to your design stage. We provide the log package but want to note clearly that you will require general contractor to finish your home after the delivery and assembly</p>
                                    {/* <p>The Log Connection supplies a <strong>complete post and beam log shell</strong> as shown at left, which includes all the log components and features listed below. You will require a general contractor to finish your home to a “lock-up” or “turn-key” stage. </p> */}
                                </MDBCol>
                            </MDBRow>
                        </MDBCol>
                        <MDBCol md="6" className={STYLES.mainRight}>
                            <img className="disablecopy" src='/images/building-styles/post-and-beam/whats-included/pb_wi_1.jpg' />
                        </MDBCol>
                    </MDBRow>
                </MDBContainer>
                <WiComponent type={`PB`} />
                <NextPageButton pageUrl={PB_URL_PATH.SP} pageName={PB_TITLE.SP} />
            </section>
        </>
    )
}


export default PB_WhatsIncludedPage;