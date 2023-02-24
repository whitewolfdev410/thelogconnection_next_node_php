import React from "react";
import { MDBContainer } from "mdbreact";
import STYLES from '../../styles/Common.module.scss';
import Carousel from 'react-bootstrap/Carousel';

export const BannerSection = ({ img }) => {

    /*
    |--------------------------------------------------------------------------
    | Images and videos
    |--------------------------------------------------------------------------
    |
    */

    const images = [
        { id: 1, title: '', description: '', src: `${process.env.IMG_BASE_URL}/home/_banner/1.jpg` },
        { id: 2, title: '', description: '', src: `${process.env.IMG_BASE_URL}/home/_banner/2.jpg` },
        { id: 3, title: '', description: '', src: `${process.env.IMG_BASE_URL}/home/_banner/3.jpg` },
        { id: 4, title: '', description: '', src: `${process.env.IMG_BASE_URL}/home/_banner/4.jpg` },
        { id: 5, title: '', description: '', src: `${process.env.IMG_BASE_URL}/home/_banner/5.jpg` },
        { id: 6, title: '', description: '', src: `${process.env.IMG_BASE_URL}/home/_banner/6.jpg` },
        { id: 7, title: '', description: '', src: `${process.env.IMG_BASE_URL}/home/_banner/7.jpg` },
        { id: 8, title: '', description: '', src: `${process.env.IMG_BASE_URL}/home/_banner/8.jpg` },
        { id: 9, title: '', description: '', src: `${process.env.IMG_BASE_URL}/home/_banner/9.jpg` }
    ];

    const videos = [
        // { id: 10, title: '', description: '', srcMp4: `${process.env.IMG_BASE_URL}/home/_banner/video1.mp4`, srcWebm: `${process.env.IMG_BASE_URL}/home/_banner/video1.webm` }
    ];

    return (
        <section>
            <MDBContainer fluid className={`${STYLES.bannerCont} ${STYLES.boxShadow1}`}>
                <div className={STYLES.bannerImgCont}>
                    {
                        img &&
                        <img
                            className="d-block w-100 banner-image disablecopy"
                            src={img}
                            alt="Home plan image"
                        />
                    }
                    {
                        !img &&
                        <Carousel fade interval={4000} controls={false}>
                            {
                                images.map(image => (
                                    <Carousel.Item key={image.id}>
                                        <img
                                            className="d-block w-100 banner-image disablecopy"
                                            src={image.src}
                                            alt={image.title}
                                        />
                                        <Carousel.Caption>
                                            {image.title && <h3>{image.title}</h3>}
                                            {image.title && <p>{image.description}</p>}
                                        </Carousel.Caption>
                                    </Carousel.Item>
                                ))
                            }
                            {
                                videos.map(video => (
                                    <Carousel.Item key={video.id}>
                                        <video width="100%" height="100%" autoplay>
                                            <source src={video.srcMp4} type="video/mp4" />
                                            <source src={video.srcWebm} type="video/webm" />
                                        </video>
                                    </Carousel.Item>
                                ))
                            }
                        </Carousel>
                    }
                </div>
                <div className={STYLES.bannerImgCont}></div>
            </MDBContainer>
        </section>
    )
}