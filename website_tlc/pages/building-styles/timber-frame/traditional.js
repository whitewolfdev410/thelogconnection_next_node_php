import { MDBContainer, MDBRow, MDBCol } from "mdbreact";
import React from "react";
import TF_STYLES from "../../../styles/BuildingStylesTF.module.scss";
import { TF_NAV_LIST, TF_TITLE, TF_URL_PATH, BUILDING_STYLES_DEFAULT_PATH, BUILDING_STYLES_CONST } from '../../../common/constants/buildingStyles';
import { BannerSection } from '../../../components/common/banner';
import { SubNavbar } from '../../../components/common/subNavbar';
import { NavigatorSection } from '../../../components/common/navigator';
import { NextPageButton } from '../../../components/common/buttons';
import Head from "next/head";

const IMG_ROOT_BANNER = `${process.env.IMG_BASE_URL}/building-styles/banner/`;

const TF_TraditionalPage = () => {
    return (
        <>
            <Head>
                <title>Timber Frame Log Home by The Log Connection</title>
                <meta property="og:title" content={`Timber Frame Log Home by The Log Connection`} />
                <meta property="og:url" content={`${process.env.DOMAIN}/building-styles/timber-frame/traditional`} />
                <meta property="og:image" content={`${process.env.DOMAIN}/images/share/Timber_Traditional.jpg`} />
                <meta property="og:description" content={`A traditional Timber Frame consists of a series of "bents", which are complete sections of the timber frame much like a large truss, typically rising to a vaulted peak. The bents are cross connected by beams and purlins to form a complete structural frame.`} />
                <meta property="og:type" content="article" />
            </Head>
            <BannerSection img={`${IMG_ROOT_BANNER}banner.jpg`} />
            <SubNavbar navBarItems={TF_NAV_LIST} header={BUILDING_STYLES_CONST.TF_HEADER_LABEL} />
            <NavigatorSection
                hrefPrev={BUILDING_STYLES_DEFAULT_PATH.STACKED_LOG_WALLS_DEFAULT}
                hrefNext={BUILDING_STYLES_DEFAULT_PATH.POST_AND_BEAM_DEFAULT}
                prevLabel={BUILDING_STYLES_CONST.SLW_HEADER_LABEL}
                nextLabel={BUILDING_STYLES_CONST.PB_HEADER_LABEL}>
            </NavigatorSection>

            <section id="main_section_traditional" className={TF_STYLES.traditional}>
                <MDBContainer >
                    <MDBRow className={TF_STYLES.section1}>
                        <MDBCol md="6" className={TF_STYLES.leftCont}>
                            <h3>Traditional Timber Frame</h3>
                            <p>A traditional Timber Frame consists of a series of "bents", which are complete sections of the timber frame much like a large truss, typically rising to a vaulted peak. The bents are cross connected by beams and purlins to form a complete structural frame.</p>
                            <p>All connections are typically mortise and tenon, in which two wood pieces are shaped to fit each other, secured by hardwood dowels or pegs. Structural stability and resistance to "racking" (sideways twisting) is achieved using similarly connected smaller braces, which may be curved for visual effect.</p>
                            <p>The timber frame is typically placed inside the frame walls, which leaves all this structural detail visible. This is a significant difference from a simpler post and beam shell, which normally uses infill frame walls between the stuctural beams and posts and would conceal the cross braces.</p>
                        </MDBCol>
                        <MDBCol md="6" className={TF_STYLES.rightCont}>
                            <div className={TF_STYLES.mainImgCont}><img className="disablecopy" src='/images/building-styles/timber-frame/traditional/tf_trad_1.jpg' /></div>
                        </MDBCol>
                    </MDBRow>
                </MDBContainer>
                <MDBContainer className={TF_STYLES.section2} >
                    <MDBRow>
                        <MDBCol md="4" sm="4"><div className={TF_STYLES.box}><img className="disablecopy" src='/images/building-styles/timber-frame/traditional/tf_trad_box_1.jpg' /><span>Dowels and pendant detailing</span></div></MDBCol>
                        <MDBCol md="4" sm="4"><div className={TF_STYLES.box}><img className="disablecopy" src='/images/building-styles/timber-frame/traditional/tf_trad_box_2.jpg' /><span>Finished vaulted timber frame</span></div></MDBCol>
                        <MDBCol md="4" sm="4"><div className={TF_STYLES.box}><img className="disablecopy" src='/images/building-styles/timber-frame/traditional/tf_trad_box_3.jpg' /><span>Timber posts with tenon and pockets</span></div></MDBCol>
                        <MDBCol md="4" sm="4"><div className={TF_STYLES.box}><img className="disablecopy" src='/images/building-styles/timber-frame/traditional/tf_trad_box_4.jpg' /><span>Complex angled cuts on ends of diagonal timbers</span></div></MDBCol>
                    </MDBRow>
                </MDBContainer>
                <NextPageButton pageUrl={TF_URL_PATH.WI} pageName={TF_TITLE.WI} />
            </section>
        </>
    )
}

export default TF_TraditionalPage;