
import React, { useState, useEffect } from "react";
import { useRouter } from "next/router";
import { BannerSection } from '../../components/common/banner';
import { SubNavbar } from '../../components/common/subNavbar';
import { PROJECTS_DETAILS_PATH, PROJECTS_DETAILS_CONST } from '../../common/constants/projects';
import { PlansDisplaySection } from "../../components/projects/plansDisplay";
import { getProjectDetails } from "../../common/services/projects";
import { ProjectsCarouselSection } from "../../components/projects/projectsCarousel";
import { NavigatorSection } from "../../components/common/navigator";
import Head from "next/head";

const IMG_BANNER_FILE_ROOT = `${process.env.IMG_BASE_URL}/projects/_banner/`;

const organizePlanData = (data) => {
    let result = [];
    if (Array.isArray(data)) {
        for (let i = 0; i < data.length; i++) {
            //result.push(`${IMG_FILE_ROOT}${data[i].file}`);
            result.push(data[i].imageUrl);
        }
    }
    return result;
}

const ProjectFloorPlan = ({ projectCode }) => {

    const router = useRouter();

    const [display, setDisplay] = useState([]);


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
                let imgArr = organizePlanData(data.plans.images);
                setDisplay(imgArr);

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
                <title>{projectCode} Floor Plans - Log Home Project by the Log Connection</title>
                <meta property="og:image" content={`${process.env.IMG_BASE_URL}/projects/${projectCode}/thumbnail.jpg`} />
                <meta property="og:type" content="article" />
                <meta property="og:url" content={`${PROJECTS_DETAILS_PATH.FLOOR_PLAN}/${projectCode}`} />
                <meta property="og:title" content={`${projectCode} Floor Plans - Log Home Project by the Log Connection`} />
                <meta property="og:description" content={`Floor plans for ${projectCode}`} />
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
            <PlansDisplaySection data={display} />
            <ProjectsCarouselSection filter={'past'} />
        </>
    );

}

export default ProjectFloorPlan;