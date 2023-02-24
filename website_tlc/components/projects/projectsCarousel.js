import React, { useState, useEffect } from "react";
import { getProjectLists } from '../../common/services/projects';
import P_STYLES from "../../styles/projects/Projects.module.scss";
import { MDBContainer } from "mdbreact";
import Carousel from 'react-grid-carousel';
import { useRouter } from "next/router"
import { ProjectCarouselNextBtn, ProjectCarouselPrevBtn } from "../../components/common/buttons";
import { CommonTitle1 } from '../../components/common/labels';
import { projectElevationsUrl } from "../common/projectUrl";

export const ProjectsCarouselSection = (props) => {

    const router = useRouter();

    const [projectList, setProjectList] = useState([]);

    useEffect(() => {
        getProjectLists(props.filter).then((data) => {
            setProjectList(data);
        }).catch((err) => {
            return (
                <h2>Error</h2>
            )
        });
    }, []);

    return (
        <section className={P_STYLES.projectCarousel}>
            <MDBContainer>
                <CommonTitle1 title={"Other Past Projects"} />
                <Carousel
                    cols={3}
                    rows={1}
                    gap="1%"
                    autoplay={10000}
                    loop
                    arrowLeft={<ProjectCarouselPrevBtn />}
                    arrowRight={<ProjectCarouselNextBtn />}
                    responsiveLayout={[
                        {
                            breakpoint: 767,
                            cols: 1,
                            rows: 1,
                            gap: 10
                        }
                    ]}
                    mobileBreakpoint={320}>

                    {projectList.map((p, i) => (
                        <Carousel.Item key={i}>
                            <a href={projectElevationsUrl(p.ProjectCode)}>
                                <div className={P_STYLES.projectCards}>
                                    <div style={{ height: '200px', overflow: 'hidden' }}>
                                        <img className="disablecopy" src={p.Thumbnail} alt={p.DisplayName} />
                                    </div>
                                    <div className={P_STYLES.label}>{p.DisplayName}</div>
                                </div>
                            </a>
                        </Carousel.Item>
                    ))}
                </Carousel>
            </MDBContainer>
        </section>
    );
}

