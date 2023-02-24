import React, { useState, useEffect } from "react";
import { MDBRow, MDBCol, MDBBtn, MDBNavbar, MDBNavbarBrand } from "mdbreact";
import STYLES from "../../styles/projects/HomeOurProjects.module.scss";
import P_STYLES from "../../styles/projects/Projects.module.scss";
import { getProjectLists } from '../../common/services/projects';
import { useRouter } from "next/router";
import { projectElevationsUrl } from "../common/projectUrl";

export const OurProjectsSection = () => {

    const router = useRouter();
    const [projectDisplayArr, setProjectDisplayArr] = useState([]);
    const [projectArr, setProjectArr] = useState([]);
    const [filter, setFilter] = useState({
        isCurrent: true,
        isPast: false
    });

    useEffect(() => {
        getProjectLists().then((data) => {
            setProjectArr(data);

            let past = data.filter((x) => {
                return x.Status === 'past';
            });

            let current = data.filter((x) => {
                return x.Status === 'current';
            });

            if (past && past.length > 1) {
                setProjectDisplayArr((past.slice(0, 3)));
                setFilter({
                    isCurrent: false,
                    isPast: true
                });
            } else if (current && current.length > 1) {
                setProjectDisplayArr((current.slice(0, 3)));
                setFilter({
                    isCurrent: true,
                    isPast: false
                });
            }

        }).catch((err) => {
            return (
                <h2>Error</h2>
            )
        });
    }, []);

    const goToPlan = (projectCode) => {
        router.push({ pathname: `/projects/details/elevations/${projectCode}` });
    }

    const goToProjects = () => {
        router.push({ pathname: '/projects/current' });
    }

    const goToPastProjects = () => {
        router.push({ pathname: '/projects/past' });
    }

    const onClickFilter = (filter) => {
        if (filter === 'current') {
            setFilter({
                isCurrent: true,
                isPast: false
            });
        } else if (filter === 'past') {
            setFilter({
                isCurrent: false,
                isPast: true
            });
        }

        let temp = projectArr.filter((x) => {
            return x.Status === filter;
        });
        setProjectDisplayArr(temp.slice(0, 3));
    }

    return (
        <section className="mt-5">
            <MDBNavbar expand="md" className={STYLES.navbarCont}>
                <div className="home-page-section">
                    <MDBNavbarBrand>
                        <span className={`${STYLES.title} text-white`}>Our Projects</span>
                    </MDBNavbarBrand>
                </div>
            </MDBNavbar>
            <div className="home-page-section">
                <MDBRow>
                    <MDBCol size="12" className={`${STYLES.tab} mt-3`}>
                        <a className={`${STYLES.tabTitle} ml-md-1 ${filter.isPast === true ? STYLES.active : STYLES.notActive}`} onClick={() => onClickFilter('past')}>Past Projects</a>
                        <a className={`${STYLES.tabTitle} ml-md-5 ml-4 ${filter.isCurrent === true ? STYLES.active : STYLES.notActive}`} onClick={() => onClickFilter('current')}>Current Projects</a>
                    </MDBCol>
                </MDBRow>
                <MDBRow center className={`${P_STYLES.projectList} mb-3 mt-4 text-center`}>
                    {projectDisplayArr.map((p, i) => (
                        <MDBCol md="4" sm="12" key={i} className="mx-2 mx-md-0 mb-3 px-md-0">
                            <a href={projectElevationsUrl(p.ProjectCode)}>
                                <div className={`${P_STYLES.projectCards} mx-4`}>
                                    <div style={{ height: '200px', overflow: 'hidden' }}>
                                        <img className="disablecopy" src={p.Thumbnail} />
                                    </div>
                                    <div className={P_STYLES.label}>{p.DisplayName}</div>
                                </div>
                            </a>
                        </MDBCol>
                    ))}
                </MDBRow>
                <MDBRow center>
                    <MDBBtn
                        size="lg"
                        className={STYLES.redirectBtn}
                        onClick={() => {
                            filter.isCurrent ? goToProjects() : goToPastProjects()
                        }}
                    >
                        VIEW ALL
                    </MDBBtn>
                </MDBRow>
            </div>
        </section>
    );

}
