
import React from "react";
import { BannerSection } from '../../components/common/banner';
import { SubNavbar } from '../../components/common/subNavbar';
import { PROJECTS_NAVBAR } from '../../common/constants/projects';
import { ProjectListSection } from '../../components/projects/projectList';
import Head from "next/head";

const IMG_BANNER_FILE_ROOT = `${process.env.IMG_BASE_URL}/projects/_banner/`;

const PastProjectPage = () => {

  return (
    <>
      <Head>
        <title>Past Projects by The Log Connection</title>
        <meta property="og:title" content={`Past Projects by The Log Connection`} />
        <meta property="og:url" content={`${process.env.DOMAIN}/projects/past`} />
        <meta property="og:image" content={`${process.env.DOMAIN}/images/share/The_Log _Connection_Logo_Square.jpg`} />
        <meta property="og:description" content={``} />
        <meta property="og:type" content="article" />
      </Head>
      <BannerSection img={`${IMG_BANNER_FILE_ROOT}project_banner_1.jpg`} />
      <SubNavbar navBarItems={PROJECTS_NAVBAR} header={'Our Projects'} />
      <ProjectListSection filter="past" />
    </>
  );

}

export default PastProjectPage;
