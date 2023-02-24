import { MDBContainer, MDBRow, MDBCol } from "mdbreact";
import React from "react";
import PB_STYLES from "../../../styles/BuildingStylesPB.module.scss";
import { PB_NAV_LIST, PB_TITLE, BUILDING_STYLES_DEFAULT_PATH, BUILDING_STYLES_CONST, PB_URL_PATH } from '../../../common/constants/buildingStyles';
import { BannerSection } from '../../../components/common/banner';
import { SubNavbar } from '../../../components/common/subNavbar';
import { NavigatorSection } from '../../../components/common/navigator';
import { NextPageButton } from '../../../components/common/buttons';
import Head from "next/head";

const IMG_ROOT_BANNER = `${process.env.IMG_BASE_URL}/building-styles/banner/`;

const PB_FourSquarePage = () => {

    return (
        <>
            <Head>
                <title>The Four Square Method by The Log Connection</title>
                <meta property="og:title" content={`The Four Square Method by The Log Connection`} />
                <meta property="og:url" content={`${process.env.DOMAIN}/building-styles/post-and-beam/four-square`} />
                <meta property="og:image" content={`${process.env.DOMAIN}/images/share/4Square.jpg`} />
                <meta property="og:type" content="article" />
                <meta property="og:description" content={`The 4Square™ method is an increasingly more technical system of beautifully hand crafted square timber joinery that still utilizing the natural round logs. This post and beam system is exclusive to The Log Connection but we expect it will be simulated throughout the industry quite quickly.`} />
            </Head>
            <BannerSection img={`${IMG_ROOT_BANNER}banner.jpg`} />
            <SubNavbar navBarItems={PB_NAV_LIST} header={BUILDING_STYLES_CONST.PB_HEADER_LABEL} />
            <NavigatorSection
                hrefPrev={BUILDING_STYLES_DEFAULT_PATH.TIMBER_FRAME_DEFAULT}
                hrefNext={BUILDING_STYLES_DEFAULT_PATH.STACKED_LOG_WALLS_DEFAULT}
                prevLabel={BUILDING_STYLES_CONST.TF_HEADER_LABEL}
                nextLabel={BUILDING_STYLES_CONST.SLW_HEADER_LABEL}>
            </NavigatorSection>

            <section id="main_section_4square" className={PB_STYLES.fourSquare}>
                <MDBContainer className={PB_STYLES.section1}>
                    <MDBRow>
                        <MDBCol md="6" className={PB_STYLES.leftCont}>
                            <h3>The Four Square Post and Beam</h3>
                            <p>The 4Square&#8482; method is an increasingly more technical system of beautifully hand crafted square timber joinery that still utilizing the natural round logs. This post and beam system is exclusive to The Log Connection but we expect it will be simulated throughout the industry quite quickly.</p>
                            <p>We created the 4Square&#8482; post and beam system to merge the endless design flexibility of our round log post and beam homes with the highly detail square connections traditionally found in timber frame joinery. This system incorporates precision wood working techniques, demanding accuracy and modern sculpting to produce a unique and highly refined looking post and beam log home.</p>
                            <p>The intriguing look of the joinery is a combination of engineering, concealed dovetails and locking mortise and tenon joinery. The strength and connection integrity are unmatched and incorporate techniques commonly used in high seismic areas like  Indonesia, China, and Japan. This building systems is becoming more frequently used as the building regulations, energy codes and engineering specification all become more stringent in Canada and United States.</p>
                            {/* <p>Our log home designers are working on a new collection of plans that will be exclusive to the 4Square&#8482; system and expect to introduce them around May of 2014. Until then we will guide you to our existing log home plans and remind you that all of the designs can be converted to suited this building style.</p> */}
                            <p>If you have an existing house design we can quote your plans via <a href="mailto:loghomes@thelogconnection.com?subject=Quote 4Square log home plans.">Email</a>.</p>
                            <p>Without becoming over technical we have the following photographs to show the joinery details and technical advantages of the system. The photograph to the left shows Western Red Cedar and the photographs below are Douglas Fir.</p>
                        </MDBCol>
                        <MDBCol md="6" className={PB_STYLES.rightCont}>
                            <div className={PB_STYLES.mainImgCont}><img className="disablecopy" src='/images/building-styles/post-and-beam/four-square/pb_4square_1.jpg' /></div>
                        </MDBCol>
                    </MDBRow>
                </MDBContainer>

                <MDBContainer >
                    <div className={PB_STYLES.section2}>
                        <MDBRow center>
                            <MDBCol md="4" sm="4"><div className={PB_STYLES.box}><img className="disablecopy" src='/images/building-styles/post-and-beam/four-square/pb_4square_box_1.jpg' /><span>Open notch showing dovetail receiver</span></div></MDBCol>
                            <MDBCol md="4" sm="4"><div className={PB_STYLES.box}><img className="disablecopy" src='/images/building-styles/post-and-beam/four-square/pb_4square_box_2.jpg' /><span>Installed beams over post with dovetail locker</span></div></MDBCol>
                            <MDBCol md="4" sm="4"><div className={PB_STYLES.box}><img className="disablecopy" src='/images/building-styles/post-and-beam/four-square/pb_4square_box_3.jpg' /><span>Top end of log posts showing tenon</span></div></MDBCol>
                        </MDBRow>
                        <MDBRow center>
                            <MDBCol md="4" sm="4"><div className={PB_STYLES.box}><img className="disablecopy" src='/images/building-styles/post-and-beam/four-square/pb_4square_box_4.jpg' /><span>Example of fully assembled 4Square package</span></div></MDBCol>
                            <MDBCol md="4" sm="4"><div className={PB_STYLES.box}><img className="disablecopy" src='/images/building-styles/post-and-beam/four-square/pb_4square_box_5.jpg' /><span>Optional bottom round anchorage system.</span></div></MDBCol>
                            <MDBCol md="4" sm="4"><div className={PB_STYLES.box}><img className="disablecopy" src='/images/building-styles/post-and-beam/four-square/pb_4square_box_6.jpg' /><span>Splice joint showing the lap area of two beams.</span></div></MDBCol>
                        </MDBRow>
                    </div>
                </MDBContainer>
                <NextPageButton pageUrl={PB_URL_PATH.LS} pageName={PB_TITLE.LS} />
            </section>
        </>
    )
}

export default PB_FourSquarePage;