import React from "react";
import Link from "next/link";
import { MDBContainer, MDBRow, MDBCol } from "mdbreact";
import styles from "../../styles/BuildingStyles.module.scss";
import { BUILDING_STYLES_NAVBAR, BUILDING_STYLES_DEFAULT_PATH } from '../../common/constants/buildingStyles';
import { BannerSection } from '../../components/common/banner';
import { SubNavbar } from '../../components/common/subNavbar';
import Flip from "react-reveal/Flip";
import Head from "next/head";

const IMG_ROOT = `${process.env.IMG_BASE_URL}/building-styles/intro/`;
const IMG_ROOT_BANNER = `${process.env.IMG_BASE_URL}/building-styles/banner/`;

const BuildingStylesPage = () => {
    return (
        <>
            <Head>
                <title>Log Home Styles by The Log Connection</title>
                <meta property="og:image" content={`${process.env.DOMAIN}/images/share/The_log_Connection_Logo_Square.jpg`} />
                <meta property="og:type" content="article" />
                <meta property="og:url" content={`${process.env.DOMAIN}/building-styles`} />
                <meta property="og:title" content={`Log Home Styles by The Log Connection`} />
                <meta property="og:description" content={``} />
            </Head>
            <BannerSection img={`${IMG_ROOT_BANNER}banner.jpg`} />
            <SubNavbar navBarItems={BUILDING_STYLES_NAVBAR} header={'Building Styles'} activePageInd={false} />
            <section className={styles.intro}>
                <MDBContainer>
                    <MDBRow center>
                        <MDBCol md="12" className={styles.titleCont}>
                            <h3>SELECT YOUR STYLE</h3>
                            <p>Over the past 32 years The Log Connection has been an industry innovator and grown to fulfill the needs of the expanding log and timber home markets. We provide the three main building styles as shown below. Please select the construction method from below to explore our building techniques and package description.</p>
                        </MDBCol>
                    </MDBRow>
                    <MDBRow center className={styles.introCardsCont}>
                        <MDBCol md="4" >
                            <Link href={{ pathname: BUILDING_STYLES_DEFAULT_PATH.POST_AND_BEAM_DEFAULT }}>
                                <div className={styles.imgCont}>
                                    <Flip left>
                                        <img src={`${IMG_ROOT}PB.jpg`} className="img-fluid disablecopy" />
                                    </Flip>
                                    <h4>Post and Beam</h4>
                                </div>
                            </Link>
                            <p>A round log post and beam home features log posts bearing on the main floor framing, supporting an upper structure of log joists, beams, and roof purlins.</p>
                        </MDBCol>
                        <MDBCol md="4">
                            <Link href={{ pathname: BUILDING_STYLES_DEFAULT_PATH.STACKED_LOG_WALLS_DEFAULT }}>
                                <div className={styles.imgCont}>
                                    <Flip left>
                                        <img src={`${IMG_ROOT}SLW.jpg`} className="img-fluid disablecopy" />
                                    </Flip>
                                    <h4>Stacked Log Wall</h4>
                                </div>
                            </Link>
                            <p>A stacked log home features main floor walls which consist entirely of logs, stacked horizontally on top of each other, typically up to the beginning of the roof structure.</p>
                        </MDBCol>
                        <MDBCol md="4">
                            <Link href={{ pathname: BUILDING_STYLES_DEFAULT_PATH.TIMBER_FRAME_DEFAULT }}>
                                <div className={styles.imgCont}>
                                    <Flip left>
                                        <img src={`${IMG_ROOT}TF.jpg`} className="img-fluid disablecopy" />
                                    </Flip>
                                    <h4>Timber Frame</h4>
                                </div>
                            </Link>
                            <p>A squared timber frame features square cut timber posts and beams, typically featuring detailed knee braces and traditional joinery.</p>
                        </MDBCol>
                    </MDBRow>
                </MDBContainer>
            </section>
        </>
    );
}

export default BuildingStylesPage;
