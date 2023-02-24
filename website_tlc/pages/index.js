import React, { Component } from "react";
import { AboutSection } from "../components/main/aboutSection";
import { ReadMoreSection } from "../components/main/readMoreSection";
import { HomeOfTheMonthSection } from "../components/main/homeOfTheMonth";
import { OurProjectsSection } from "../components/projects/ourProjectsSection";
import { BannerSection } from '../components/common/banner';
import { PlanBookSection } from '../components/main/planBookSection';

class HomePage extends Component {
    render() {
        return (
            <>
                <BannerSection />
                <AboutSection />
                <ReadMoreSection />
                <HomeOfTheMonthSection />
                <OurProjectsSection />
                <PlanBookSection />
            </>
        );
    }
}

export default HomePage;
