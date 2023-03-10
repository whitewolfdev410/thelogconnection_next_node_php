import { MDBContainer, MDBRow, MDBCol } from "mdbreact";
import React from "react";
import TF_STYLES from "../../../styles/BuildingStylesTF.module.scss";
import { TF_NAV_LIST, BUILDING_STYLES_DEFAULT_PATH, BUILDING_STYLES_CONST } from '../../../common/constants/buildingStyles';
import { BannerSection } from '../../../components/common/banner';
import { SubNavbar } from '../../../components/common/subNavbar';
import { NavigatorSection } from '../../../components/common/navigator';
import { NextPageButton } from '../../../components/common/buttons';
import { LogSpeciesModal } from '../../../components/building-styles/logSpecies';
import Head from "next/head";

const IMG_ROOT_BANNER = `${process.env.IMG_BASE_URL}/building-styles/banner/`;

const TF_LogSpeciesPage = () => {
    return (
        <>
            <Head>
                <title>Wood Species use in Stacked Log Homes</title>
                <meta property="og:title" content={`Wood Species use in Stacked Log Homes`} />
                <meta property="og:url" content={`${process.env.DOMAIN}/building-styles/timber-frame/log-species`} />
                <meta property="og:image" content={`${process.env.DOMAIN}/images/share/The_Log _Connection_Logo_Square.jpg`} />
                <meta property="og:description" content={``} />
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
            <section className={TF_STYLES.logSpecies}>
                <MDBContainer className={TF_STYLES.section1} >
                    <MDBRow>
                        <MDBCol md="12" sm="12" className={TF_STYLES.rightCont}>
                            <h3>Log Species for Timber Frame Homes</h3>
                            <p>Our location in the interior of British Columbia, Canada provides a selection of logs unsurpassed worldwide. Only premium quality logs are hand selected from trees growing at high elevations. These trees experience slower growth rates, resulting in a higher structural value due to tighter annual growth rings. In addition to strength, each log is hand selected based on straightness of grain and consistency of size.</p>
                        </MDBCol>
                    </MDBRow>
                </MDBContainer>
                <MDBContainer >
                    <MDBRow className={TF_STYLES.section2} center>
                        <MDBCol md="12" sm="12">
                            <div className={TF_STYLES.card}>
                                <img className="disablecopy" src='/images/building-styles/timber-frame/log-species/tf_ls_card_douglasFir.jpg' />
                                <h4>Douglas Fir</h4>
                                <p>Douglas Fir is a very strong wood with a rich reddish heartwood. Generally tight growth rings, straight grain, medium heavy with good resistance to mildew and fungus.</p>
                                <p>Due to its high strength, Fir is a perfect choice for both wall logs and beams and joists. Its weight may make transportation costs slightly higher.</p>
                                <LogSpeciesModal logSpecie={'DF'} />
                            </div>
                        </MDBCol>
                    </MDBRow>
                </MDBContainer>
                <NextPageButton pageUrl={`/home-plans`} pageName={`Go to Home Plans`} />
            </section>
        </>
    )


}

export default TF_LogSpeciesPage;