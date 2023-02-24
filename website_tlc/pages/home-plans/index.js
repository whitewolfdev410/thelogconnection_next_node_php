import React, { useState, useEffect } from "react";
import { Typeahead } from 'react-bootstrap-typeahead';
import { MDBContainer, MDBCardImage } from "mdbreact";
import { useRouter } from "next/router";
import STYLES from "../../styles/home-plans/HomePlan.module.scss";
import { FloaterNav } from "../../components/home-plans/floaterNav";
import { SubNavbarHomePlan } from '../../components/common/subNavbar';
import { getHomePlan } from '../../common/services/home-plans';
import Select from 'react-select';
import Head from "next/head";
import { homePlanFloorPlanUrl } from "../../components/common/homePlanUrl";

const IMG_ROOT = `${process.env.IMG_BASE_URL}/home-plans/_main/banner.jpg`;

const HomePlansPage = () => {

    const router = useRouter();

    /*
    |--------------------------------------------------------------------------
    | Home style
    |--------------------------------------------------------------------------
    |
    */

    const style = router.query.style;

    /*
    |--------------------------------------------------------------------------
    | Sort by options
    |--------------------------------------------------------------------------
    |
    */

    const sortByOptions = [
        { label: 'Name', value: 'name' },
        { label: 'Size', value: 'size' }
    ];

    /*
    |--------------------------------------------------------------------------
    | Sort direction options for size
    |--------------------------------------------------------------------------
    |
    */

    const sortDirectionOptionsForSize = [
        { label: 'High to low', value: 'desc' },
        { label: 'Low to high', value: 'asc' }
    ];

    /*
    |--------------------------------------------------------------------------
    | Sort direction options for name
    |--------------------------------------------------------------------------
    |
    */

    const sortDirectionOptionsForName = [
        { label: 'Z -> A', value: 'desc' },
        { label: 'A -> Z', value: 'asc' }
    ];

    /*
    |--------------------------------------------------------------------------
    | Filter parameters
    |--------------------------------------------------------------------------
    |
    */

    const [sortBy, setSortBy] = useState('name');
    const [sortDirection, setSortDirection] = useState('asc');

    const [allHomePlans, setAllHomePlans] = useState([]);
    const [gallery, setGallery] = useState([]);

    /*
    |--------------------------------------------------------------------------
    | Get filter parameters
    |--------------------------------------------------------------------------
    |
    */

    const getFilterParameters = () => {
        return {
            sort_by: sortBy,
            sort_direction: sortDirection
        }
    }

    useEffect(() => {
        let filterParameters = getFilterParameters();

        getHomePlan("all", filterParameters)
            .then((data) => {
                setAllHomePlans([...data]);

                if (style) {
                    const filtered = data.filter(c => c.Style === style);
                    setGallery([...filtered]);
                } else {
                    setGallery([...data]);
                }
            });
    }, [sortBy, sortDirection]);

    let typeahead = null;
    const filterHomePlansByName = (name) => {
        if (name) {
            if (allHomePlans && Array.isArray(allHomePlans)) {
                allHomePlans.map(homePlan => {
                    if (homePlan.name === name) {
                        router.push({ pathname: '/home-plans/details/floor-plans', query: { plan: homePlan.planCode, sortBy: sortBy, sortDirection: sortDirection, scroll: false } });
                    }
                });
            }
        }
        typeahead.clear();
    }

    const filterHomePlanByStyle = (filter) => {
        if (filter === 'All') {
            setGallery(allHomePlans);
        } else {
            const filtered = allHomePlans.filter(c => c.Style === filter);
            setGallery(filtered);
        }
    };

    return (
        <>
            <Head>
                <title>Log Home Plans by The Log Connection</title>
                <meta property="og:image" content={`${process.env.DOMAIN}/images/share/The_log_Connection_Logo_Square.jpg`} />
                <meta property="og:type" content="article" />
                <meta property="og:url" content={`${process.env.DOMAIN}/home-plans`} />
                <meta property="og:title" content={`Log Home Plans by The Log Connection`} />
                <meta property="og:description" content={``} />
            </Head>
            <section className={`${STYLES.homePlan}`}>
                <MDBContainer fluid className="p-0">
                    <div className={`${STYLES.banner}`}>
                        <div className={`${STYLES.content}`}>
                            <img className="disablecopy" src={IMG_ROOT} />
                        </div>
                        <div className={`${STYLES.overlay}`}>
                            <p className={`${STYLES.titleLbl} text-center`}>Log and Timber Homes</p>
                            <p className={STYLES.searchLbl}>Search a Plan:</p>
                            <div className="row mb-3">
                                <div className="col-6">
                                    <Select
                                        options={sortByOptions}
                                        value={sortByOptions.find(sortByOption => sortByOption.value === sortBy)}
                                        onChange={selectedOption => setSortBy(selectedOption.value)}
                                    />
                                </div>
                                <div className="col-6">
                                    <Select
                                        options={sortBy === 'name' ? sortDirectionOptionsForName : sortDirectionOptionsForSize}
                                        value={sortBy === 'name' ? sortDirectionOptionsForName.find(sortDirectionOption => sortDirectionOption.value === sortDirection) : sortDirectionOptionsForSize.find(sortDirectionOption => sortDirectionOption.value === sortDirection)}
                                        onChange={selectedOption => setSortDirection(selectedOption.value)}
                                    />
                                </div>
                            </div>
                            <Typeahead labelKey={option => `${option.name} ~ ${option.sf} SQ. FT.`}
                                id="searchPlan"
                                options={allHomePlans}
                                ref={(ref) => typeahead = ref}
                                onChange={selected => { filterHomePlansByName(selected[0].name) }}
                            />
                        </div>
                    </div>
                </MDBContainer>
                <SubNavbarHomePlan filterFn={filterHomePlanByStyle} />
                <FloaterNav homePlans={allHomePlans} context="all" />
                <MDBContainer fluid className="p-0 text-center">
                    {gallery.map((hp, i) => (
                        <div className={STYLES.cards} key={i}>
                            <a href={homePlanFloorPlanUrl(hp.planCode, sortBy, sortDirection)}>
                                <div className={`${STYLES.lblCont} text-left`}>
                                    <div className={STYLES.cardLbl}><sup>The </sup>{hp.name}
                                        <span className={STYLES.cardLbl2}>{hp.sf} SQ. FT.</span></div>
                                </div>
                                <div className={`${STYLES.cardImage}`} >
                                    <MDBCardImage className={STYLES.img} src={hp.imageUrl} waves />
                                </div>
                            </a>
                        </div>
                    ))}
                </MDBContainer>
            </section>
        </>
    );
}

export default HomePlansPage;