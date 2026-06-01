import Layout from "../Layout/Layout"
import ShowtimeInformation from "./Components/ShowtimeInformation"
import ShowtimeAttributes from "./Components/ShowtimeAttributes"
import SuccessMessage from "../Components/SuccessMessage"
import { usePage } from "@inertiajs/react"
import { useForm } from '@inertiajs/react';
import { Link } from '@inertiajs/react'
import { useEffect, useState } from "react"
import ShowtimeTimetable from "./Components/ShowtimeTimetable"
import Button from "../Components/Button"

export default function Create() {
    const { theaters, formInfo } = usePage().props;
    const { flash } = usePage();
    const [ isVisible, setIsVisible ] = useState(false);

    const { movies, screens } = formInfo

    // Implement Inertia useForm hook
    const { post, data, setData, reset, processing } = useForm({
        // Use movie, theater and screen id for data integrity
        movie: movies[0].id,
        theater: theaters[0].id,
        screen: screens[0].id,
        date: '',
        time: '',
        subtitles: true,
        is_3d: false,
        dubbed: false
    })

    // Listen for success flash message
    useEffect(() => {
        if(flash.success){
            // If a showtime was created successfully, display success message for 5 seconds
            setIsVisible(true);

            setTimeout(() => {
                setIsVisible(false)
            }, 5000);
        }
    }, [flash.success]);


    const submit = (e) => {
        e.preventDefault()
        post('/showtimes', {
            preserveScroll: true,
            onSuccess: (message) => {
                reset();
            },
        });

    }

    return(
        <div>
            <div className="text-2xl font-bold mb-10">Showtimes Timetable</div>
            {/**
             * Display destroy success message at the top of the form
             */}
            <div className={`${flash.type === 'delete' && isVisible ? '' : 'hidden'}`}>
                <SuccessMessage message={flash.success} />
            </div>
            <ShowtimeTimetable />

            <form onSubmit={submit} action="/showtimes">
                <div className="space-y-12">
                    <ShowtimeInformation data={data} 
                                         setData={setData}
                    />

                    <ShowtimeAttributes data={data}
                                        setData={setData} 
                    />
                </div>
                {/**
                 * Display store and update success messages at the bottom of the form
                */}
                <div className={`${flash.type !== 'delete' && isVisible ? '' : 'hidden'}`}>
                    <SuccessMessage message={flash.success} />
                </div>

                <div className="mt-6 flex items-center justify-end gap-x-6">
                    <Button as={Link}
                            color='red'
                            type='button'
                            href='/'
                    >
                        Cancel
                    </Button>
                    <Button as='button'
                            color='indigo'
                            type='submit'
                            disabled={processing}
                    >
                        Save
                    </Button>
                </div>
            </form>
        </div>
    )
}

Create.layout = page => <Layout children={page} slot="Create Showtime" />