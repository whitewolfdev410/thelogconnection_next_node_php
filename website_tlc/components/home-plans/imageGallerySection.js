import { MDBContainer, MDBRow, MDBCol } from "mdbreact";
import P_STYLES from "../../styles/home-plans/ImageGallery.module.scss";
import React, { useState, useEffect } from "react";
import { getHomePlan, getHomePlanImages } from "../../common/services/home-plans";
import Carousel from 'react-grid-carousel';
import ReactPlayer from 'react-player';
import ANIMATION from "../../styles/Animation.module.scss";

const CarouselPrevBtn = () => {
    return (
        <div className={P_STYLES.carouseLeftBtn}>
            <img src='/images/common/media-left-arrow.png' />
        </div>
    )
}

const CarouselNextBtn = () => {
    return (
        <div className={P_STYLES.carouselRightBtn}>
            <img src='/images/common/media-right-arrow.png' />
        </div>
    )
}

export const ImageGallerySection = ({ homePlan }) => {

    const planCode = homePlan.planCode;

    /*
    |--------------------------------------------------------------------------
    | Flag: Details related to rotation functionality in images grid
    |--------------------------------------------------------------------------
    |
    */

    const [imageSelected, setImageSelected] = useState(false);
    const [shouldRotate, setShouldRotate] = useState(true);

    useEffect(() => {
        if (imageSelected) {
            setShouldRotate(false);
        }
    }, [imageSelected]);

    const [galleryData, setGalleryData] = useState([]);
    const [videoThumbnail, setVideoThumbnail] = useState({
        imageUrl: "",
        videoUrl: "",
        isPlaying: false
    });
    const [activeItem, setActiveItem] = useState([]);

    useEffect(() => {
        getHomePlan(planCode).then((data) => {
            if (data && data[0]) {
                getHomePlanImages(planCode).then((images) => {
                    setGalleryData(images);
                    if (data[0].videoURL) {
                        setActiveItem({
                            url: data[0].videoURL,
                            type: "video"
                        });
                        setVideoThumbnail({
                            imageUrl: data[0].imageUrl,
                            videoUrl: data[0].videoURL,
                            isPlaying: true
                        })
                    } else {
                        setActiveItem({
                            imageUrl: images[0].imageUrl,
                            type: "image",
                            caption: images[0].caption
                        });
                        setVideoThumbnail({
                            imageUrl: "",
                            videoUrl: "",
                            isPlaying: false
                        });
                    }
                }).catch((err) => {
                    return (<h2>ERROR...</h2>)
                });

            }
        }).catch((err) => {
            return (<h2>ERROR...</h2>)
        });
    }, [planCode]);

    const onClickItem = (item) => {
        setActiveItem(item);
    }

    const onClickPlayBtn = (item) => {
        setVideoThumbnail({
            imageUrl: item.imageUrl,
            videoUrl: item.videoUrl,
            isPlaying: !item.isPlaying
        })
        setActiveItem({
            url: item.videoUrl,
            type: "video",
            caption: item.caption
        });
    }

    const customDot = ({ isActive }) => (
        <span
            style={{
                marginTop: "150px",
                display: 'inline-block',
                height: isActive ? '10px' : '7px',
                width: isActive ? '10px' : '7px',
                background: isActive ? 'yellow' : 'white',
                borderRadius: "50%"
            }}
        ></span>
    )

    return (
        <section className={P_STYLES.galleryCont}>
            <MDBContainer className="mt-1">
                <div className={P_STYLES.gallery}>
                    <MDBRow center>
                        <MDBCol md="9" sm="12" lg="9" xl="9">
                            <div className={P_STYLES.displayCont}>
                                {activeItem.type === 'video' ?
                                    <div className={P_STYLES.videoCont}>
                                        <ReactPlayer
                                            controls={true}
                                            url={activeItem.url}
                                            className="react-player"
                                            playing={videoThumbnail.isPlaying}
                                            width="100%"
                                            height="100%"
                                        />
                                    </div>
                                    :
                                    <div className={`${P_STYLES.imgDispCont} ${ANIMATION.fadeIn} m-3`} key={new Date()}>
                                        <div className={P_STYLES.sectionLabel}>{activeItem?.caption}</div>
                                        <div className={P_STYLES.imgCont}>
                                            <img className="disablecopy" src={activeItem.imageUrl} />
                                        </div>
                                    </div>}
                            </div>
                        </MDBCol>

                        <MDBCol md="3" sm="12" lg="3" xl="3" className="p-0">
                            {videoThumbnail && videoThumbnail.videoUrl ?
                                <div>
                                    <div className={P_STYLES.sectionLabel}>Video</div>
                                    <div className={P_STYLES.videoThumbnailCont}>
                                        <img className="disablecopy" src={videoThumbnail?.imageUrl} />
                                        <div className={P_STYLES.playButton} onClick={() => onClickPlayBtn(videoThumbnail)} />
                                    </div>
                                </div> : <></>}
                            {galleryData && galleryData.length > 0 ?
                                <div>
                                    <div className={P_STYLES.carouselCont}>
                                        <div className={P_STYLES.sectionLabel}>Still Images</div>
                                        <Carousel
                                            arrowLeft={galleryData.length && galleryData.length > 12 ? <CarouselPrevBtn /> : (<></>)}
                                            arrowRight={galleryData.length && galleryData.length > 12 ? <CarouselNextBtn /> : (<></>)}
                                            cols={3}
                                            rows={4}
                                            gap={10}
                                            loop
                                            autoplay={shouldRotate ? 10000 : undefined}
                                            dot={customDot}
                                            showDots={galleryData.length && galleryData.length > 12 ? true : false}
                                            responsiveLayout={[
                                                {
                                                    breakpoint: 767,
                                                    cols: 3,
                                                    rows: 1,
                                                    gap: 20
                                                }
                                            ]}
                                            mobileBreakpoint={320}>
                                            {galleryData.map((g, i) => (
                                                <Carousel.Item key={i}>
                                                    <div
                                                        className={P_STYLES.thumbnailCont}
                                                        onClick={() => setImageSelected(true)}
                                                    >
                                                        <img className="enablecopy" src={g.imageUrl} onClick={() => onClickItem(g)} />
                                                    </div>
                                                </Carousel.Item>
                                            ))}
                                        </Carousel>
                                    </div>
                                    <div className={P_STYLES.viewMoreCont}>
                                        {galleryData.length && galleryData.length > 12 ?
                                            <div className={P_STYLES.viewMore}>View More</div> : <></>
                                        }
                                    </div>
                                </div> : <></>}
                        </MDBCol>
                    </MDBRow>
                </div>
            </MDBContainer>
        </section>
    )
}

export default ImageGallerySection;
