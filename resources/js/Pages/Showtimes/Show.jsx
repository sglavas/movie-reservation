import Layout from "../Components/Layout"
import Button from "../Components/Button";
import { Link } from "@inertiajs/react";
import ShowtimeDetails from "./Components/ShowtimeDetails";
import { useState, useEffect } from "react";
import { usePage } from "@inertiajs/react";
import SuccessMessage from "../Components/SuccessMessage";

export default function Show({showtime}){
    const { flash, url } = usePage();
    const [ isVisible, setIsVisible ] = useState(false);

    const handleClick = () => {
        if(!window.confirm('Are you sure that you want to delete the showtime?')){
            e.preventDefault();
        };
    }

    useEffect(() => {
        if(flash.success === 'Showtime updated successfully'){
            setIsVisible(true);
    
            setTimeout(() => {
                setIsVisible(false)
            }, 5000);
        }
    }, [])

    return(
        <div className="flex flex-col items-center justify-center">
            <ShowtimeDetails />
            <div className="my-5 flex flex-row gap-10">
                <Button as={Link}
                        color='red'
                        href={url}
                        onClick={handleClick}
                        method='delete'
                >
                    Delete
                </Button>
                <Button as={Link}
                        color='indigo'
                        href={`/showtimes/${showtime.id}/edit`}
                >
                    Edit
                </Button>
            </div>
            <div className={`${isVisible ? '' : 'hidden'} my-10`}>
                <SuccessMessage message={flash.success} />
            </div>
        </div>
    )
}

Show.layout = page => <Layout children={page} slot="View Showtime" />
