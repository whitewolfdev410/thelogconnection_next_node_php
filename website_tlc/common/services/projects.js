import { getData } from '../webService';

export const getProjectLists = (filter) => {
    let DATA_URL = `${process.env.PHP_ENDPOINT}?function=GetProjectList&params=`

    switch (filter) {
        case 'current':
            DATA_URL = `${process.env.PHP_ENDPOINT}?function=GetProjectList&params=current`;
            break;
        case 'past':
            DATA_URL = `${process.env.PHP_ENDPOINT}?function=GetProjectList&params=past`;
            break;
        case 'favorite':
            DATA_URL = `${process.env.PHP_ENDPOINT}?function=GetProjectList&params=favorite`;
            break;
    }

    return getData(DATA_URL);
} 

export const getProjectDetails = (projectCode) => {
    let DATA_URL = `${process.env.PHP_ENDPOINT}?function=GetProjectDetails&params=${projectCode}`;
    return getData(DATA_URL);
} 