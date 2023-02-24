
import React, { useState, useEffect } from "react";
import { getProjectLists } from '../../common/services/projects';
import P_STYLES from "../../styles/projects/Projects.module.scss";
import { MDBContainer, MDBPagination, MDBRow, MDBCol, MDBPageItem, MDBPageNav } from "mdbreact";
import { paginateArray } from '../../common/utilities';
import { useRouter } from "next/router"
import { projectElevationsUrl } from "../common/projectUrl";

export const ProjectListSection = (props) => {

    const router = useRouter();

    const [projectsArr, setProjectsArr] = useState([]);
    const [projectDisplayArr, setProjectDisplayArr] = useState([]);
    const [pages, setPages] = useState([]);
    const [activePage, setActivePage] = useState(0);


    useEffect(() => {
        getProjectLists(props.filter).then((data) => {
            let paginatedArr = paginateArray(data, 12);
            let pagesArr = Array.from({ length: paginatedArr.length }, (v, i) => i);
            setPages(pagesArr);
            setProjectsArr(paginatedArr);
            setProjectDisplayArr(paginatedArr[0]);
        }).catch((err) => {
            return (
                <h2>Error</h2>
            )
        });
    }, []);

    const onPageNoClick = (pageNo) => {
        setActivePage(pageNo);
        setProjectDisplayArr(projectsArr[pageNo]);
    }

    const onClickRight = () => {
        let nextPageNo = activePage + 1;
        setProjectDisplayArr(projectsArr[nextPageNo]);
        setActivePage(nextPageNo);

    }

    const onClickLeft = () => {
        let nextPageNo = activePage - 1;
        setProjectDisplayArr(projectsArr[nextPageNo]);
        setActivePage(nextPageNo);
    }

    if (!Array.isArray(projectsArr) || projectsArr.length === 0) {
        return (
            <section>
                <MDBContainer>
                    <h2 className="mt-4">NO PROJECTS FOUND...</h2>
                </MDBContainer>
            </section>
        )
    }

    return (
        <>
            <section className={P_STYLES.projectPage}>
                <div className="home-page-section">
                    <MDBRow className={P_STYLES.projectList}>
                        <MDBCol md="12" sm="12" xs="12">
                            <MDBRow center>
                                {projectDisplayArr.map((p, i) => (
                                    <MDBCol md="4" sm="6" xs="6" key={i}>
                                        <a href={projectElevationsUrl(p.ProjectCode)}>
                                            <div className={P_STYLES.projectCards}>
                                                <div style={{ height: '200px', overflow: 'hidden' }}>
                                                    <img className="disablecopy" src={p.Thumbnail} />
                                                </div>
                                                <div className={P_STYLES.label}>{p.DisplayName}</div>
                                            </div>
                                        </a>
                                    </MDBCol>
                                ))}
                            </MDBRow>
                            <MDBPagination color="red">
                                <MDBPageItem disabled={activePage == 0}>
                                    <MDBPageNav className="page-link" aria-label="Previous" onClick={() => onClickLeft()}>
                                        <span aria-hidden="true">&laquo;</span>
                                        <span className="sr-only">Previous</span>
                                    </MDBPageNav>
                                </MDBPageItem>
                                {pages.map((p, i) => (
                                    <MDBPageItem key={i} active={i == activePage}>
                                        <MDBPageNav className="page-link" onClick={() => onPageNoClick(i)}>
                                            {i + 1} <span className="sr-only">(current)</span>
                                        </MDBPageNav>
                                    </MDBPageItem>
                                ))}
                                <MDBPageItem disabled={activePage == pages?.length - 1}>
                                    <MDBPageNav className="page-link" onClick={() => onClickRight()}>
                                        &raquo;
                                    </MDBPageNav>
                                </MDBPageItem>
                            </MDBPagination>
                        </MDBCol>
                    </MDBRow>
                </div>
            </section>
        </>
    );

}

