import Layout from "../Components/Layout"
import ShowtimeInformation from "./Components/ShowtimeInformation"
import ShowtimeAttributes from "./Components/ShowtimeAttributes"
import SuccessMessage from "../Components/SuccessMessage"
import { usePage } from "@inertiajs/react"
import { useForm } from '@inertiajs/react';
import { Link } from '@inertiajs/react'
import { useState } from "react"
import ShowtimeTimetable from "./Components/ShowtimeTimetable"

export default function Create() {
    const { theaters, formInfo } = usePage().props;
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

    const submit = (e) => {
        e.preventDefault()
        post('/showtimes', {
            preserveScroll: true,
            onSuccess: (message) => {
                reset();
                setIsVisible(true);

                setTimeout(() => {
                    setIsVisible(false)
                }, 5000);
            },
        });

    }

    return(
        <div>
            <div className="text-2xl font-bold mb-10">Showtimes Timetable</div>
            <ShowtimeTimetable />

            <form onSubmit={submit} action="/showtimes">
            <div className="text-2xl font-bold my-10">Create Showtime</div>
                <div className="space-y-12">
                    <ShowtimeInformation data={data} 
                                         setData={setData}
                    />

                    <ShowtimeAttributes data={data}
                                        setData={setData} 
                    />
                </div>
                <div className={`${isVisible ? '' : 'hidden'}`}>
                    <SuccessMessage message={'A showtime has been created.'} />
                </div>

                <div className="mt-6 flex items-center justify-end gap-x-6">
                    <Link
                        type="button"
                        className="text-sm/6 font-semibold text-white"
                        href='/'
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        className="rounded-md bg-indigo-600 hover:bg-indigo-500 px-3 py-2 text-sm font-semibold text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                        disabled={processing}
                    >
                        Save
                    </button>
                </div>
            </form>
        </div>
    )
}

Create.layout = page => <Layout children={page} slot="Create Showtime" />