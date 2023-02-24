import React from "react";
import { BannerSection } from '../../components/common/banner';
import { SubNavbar } from '../../components/common/subNavbar';
import { GallerySection } from '../../components/galleries/gallerySection';
import { GALLERY_NAVBAR } from '../../common/constants/gallery';
import Head from "next/head";

const IMG_BANNER_FILE_ROOT = `${process.env.IMG_BASE_URL}/gallery/_banner/`;

export const ExteriorPage = () => {

  return (
    <>
      <Head>
        <title>Log Home Exterior Gallery by The Log Connection</title>
        <meta property="og:title" content={`Log Home Exterior Gallery by The Log Connection`} />
        <meta property="og:url" content={`${process.env.DOMAIN}/galleries/exterior`} />
        <meta property="og:image" content={`${process.env.DOMAIN}/images/share/The_Log _Connection_Logo_Square.jpg`} />
        <meta property="og:description" content={``} />
        <meta property="og:type" content="article" />
      </Head>
      <BannerSection img={`${IMG_BANNER_FILE_ROOT}gallery_banner_1.jpg`} />
      <SubNavbar navBarItems={GALLERY_NAVBAR} header={'Photo Galleries'} />
      <GallerySection filter='exterior' />
    </>
  );
}

export default ExteriorPage;
