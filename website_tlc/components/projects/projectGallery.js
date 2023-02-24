import React, { useEffect, useState } from "react";
import { BannerSection } from '../../components/common/banner';
import { SubNavbar } from '../../components/common/subNavbar';
import { PROJECTS_DETAILS_PATH, PROJECTS_DETAILS_CONST } from '../../common/constants/projects';
import { ProjectGallerySection } from "../../components/projects/gallerySection";
import { ProjectsCarouselSection } from "../../components/projects/projectsCarousel";
import { getProjectDetails } from "../../common/services/projects";
import { NavigatorSection } from "../../components/common/navigator";
import Head from "next/head";

const IMG_BANNER_FILE_ROOT = `${process.env.IMG_BASE_URL}/projects/_banner/`;

const ProjectImageGallery = ({ projectCode }) => {

    const PROJECTS_DETAILS_NAVBAR = [
        { title: PROJECTS_DETAILS_CONST.ELEVATION_HEADER_LABEL, href: `${PROJECTS_DETAILS_PATH.ELEVATION}/${projectCode}` },
        { title: PROJECTS_DETAILS_CONST.FLOOR_PLAN_HEADER_LABEL, href: `${PROJECTS_DETAILS_PATH.FLOOR_PLAN}/${projectCode}` },
        { title: PROJECTS_DETAILS_CONST.GALLERY_HEADER_LABEL, href: `${PROJECTS_DETAILS_PATH.GALLERY}/${projectCode}` }
    ]

    /*
    |--------------------------------------------------------------------------
    | Project details
    |--------------------------------------------------------------------------
    |
    */

    const [status, setStatus] = useState('');

    /*
    |--------------------------------------------------------------------------
    | Next Project link
    |--------------------------------------------------------------------------
    |
    */

    const [nextProjectLink, setNextProjectLink] = useState('');

    useEffect(() => {
        getProjectDetails(projectCode)
            .then((data) => {
                setStatus(data.project.status);

                if (data.project.next_project_code) {
                    setNextProjectLink(`${PROJECTS_DETAILS_PATH.ELEVATION}/${data.project.next_project_code}`);
                } else {
                    setNextProjectLink(`${PROJECTS_DETAILS_PATH.ELEVATION}/${data.project.first_project_code}`);
                }
            });
    }, [projectCode]);

    return (
        <>
            <Head>
                <title>{projectCode} Gallery - Log Home Project by the Log Connection</title>
                <meta property="og:image" content={`${process.env.IMG_BASE_URL}/projects/${projectCode}/thumbnail.jpg`} />
                <meta property="og:type" content="article" />
                <meta property="og:url" content={`${PROJECTS_DETAILS_PATH.GALLERY}/${projectCode}`} />
                <meta property="og:title" content={`${projectCode} Gallery - Log Home Project by the Log Connection`} />
                <meta property="og:description" content={`Gallery for ${projectCode}`} />
            </Head>
            <BannerSection img={`${IMG_BANNER_FILE_ROOT}project_banner_1.jpg`} />
            <SubNavbar navBarItems={PROJECTS_DETAILS_NAVBAR} header={projectCode} />
            <NavigatorSection
                bgColor="white"
                hrefPrev={status === 'past' ? `/projects/past` : `/projects/current`}
                hrefNext={nextProjectLink}
                prevLabel={`Back to Gallery`}
                nextLabel={nextProjectLink ? `Next Project` : ''}>
            </NavigatorSection>
            <ProjectGallerySection providedProjectCode={projectCode} />
            <ProjectsCarouselSection filter={'past'} />
        </>
    );

}

export default ProjectImageGallery;