const homePlanFloorPlanUrl = (planCode, sortBy, sortDirection) => {
    if(sortBy === undefined) sortBy = 'name';
    if(sortDirection === undefined) sortBy = 'asc';
    return `/home-plans/details/floor-plans/${planCode}/?sortBy=${sortBy}&sortDirection=${sortDirection}&scroll=false`;
}

const homePlanGalleryUrl = (planCode, sortBy, sortDirection) => {
    if(sortBy === undefined) sortBy = 'name';
    if(sortDirection === undefined) sortBy = 'asc';
    return `/home-plans/details/gallery/${planCode}/?sortBy=${sortBy}&sortDirection=${sortDirection}&scroll=false`;
}

export {
    homePlanFloorPlanUrl,
    homePlanGalleryUrl
}