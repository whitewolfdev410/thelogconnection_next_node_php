import React, { useState, useEffect } from "react";
import { BannerSection } from '../../components/common/banner';
import { SubNavbar } from '../../components/common/subNavbar';
import { TLC_NAVBAR } from '../../common/constants/tlc';
import TLC_STYLES from '../../styles/tlc/TLC.module.scss';
import { getHomePlanImages } from "../../common/services/home-plans";
import { MDBContainer, MDBRow, MDBCol } from "mdbreact";
import Head from "next/head";

const IMG_BANNER_FILE_ROOT = `${process.env.IMG_BASE_URL}/tlc-monthly/banner/`;

const BuildingAwardsPage = () => {

  const [galleryData, setGalleryData] = useState([]);

  useEffect(() => {
    getHomePlanImages('Langdon').then((data) => {
      setGalleryData(data);
    }).catch((err) => {
      return (<h2>ERROR...</h2>)
    });
  }, []);

  return (
    <>
      <Head>
        <title>Log Home Building Awards for The Log Connection</title>
        <meta property="og:title" content={`Log Home Building Awards for The Log Connection`} />
        <meta property="og:url" content={`${process.env.DOMAIN}/building-awards`} />
        <meta property="og:image" content={`${process.env.DOMAIN}/images/share/The_Log _Connection_Logo_Square.jpg`} />
        <meta property="og:description" content={``} />
        <meta property="og:type" content="article" />
      </Head>
      <BannerSection img={`${IMG_BANNER_FILE_ROOT}tlc_banner_1.jpg`} />
      {/* <SubNavbar navBarItems={TLC_NAVBAR} header={"Award Winning Log Homes"} /> */}
      <SubNavbar navBarItems={TLC_NAVBAR} header={""} />
      <section className={TLC_STYLES.buildingAwards}>
        <MDBContainer size="sm">
          <MDBRow>
            <MDBCol md="8" className="p-0">
              <div className="pt-5">
                <p className={TLC_STYLES.header}>
                  CANADIAN HOME BUILDERS ASSOCIATION
                  <br />
                  AND THOMPSON OKANAGAN HOUSING AWARDS
                </p>
              </div>
              <div className="mt-5 mb-3">
                <p className={`${TLC_STYLES.subHeader} ${TLC_STYLES.borderBtm}`}>GOLD AWARD: BEST SINGLE DETACHED HOME 4,000 ~ 4,999 SQ. FT.</p>
              </div>
              <MDBRow>
                {galleryData.map((g, i) => (
                  <MDBCol md="3" sm="6" key={i}>
                    <div className={`${TLC_STYLES.imgCont} mb-3`}>
                      <img className="disablecopy" src={g.imageUrl} />
                    </div>
                  </MDBCol>
                ))}
              </MDBRow>
              <div className="mt-5 mb-3">
                <p className={`${TLC_STYLES.subHeader} ${TLC_STYLES.borderBtm}`}>SILVER AWARD FOR BEST FEATURE</p>
              </div>
              <MDBRow>
                {galleryData.map((g, i) => (
                  <MDBCol md="3" key={i}>
                    <div className={`${TLC_STYLES.imgCont} mb-3`}>
                      <img className="disablecopy" src={g.imageUrl} />
                    </div>
                  </MDBCol>
                ))}
              </MDBRow>
            </MDBCol>
            <MDBCol md="4" className="p-0">
              <div className={TLC_STYLES.trophyImgCont}>
                <img src='/images/tlc-monthly/trophy.png' className="img-fluid m-5 disablecopy" />
              </div>
            </MDBCol>
          </MDBRow>
        </MDBContainer>
      </section>
    </>
  );
}

export default BuildingAwardsPage;
