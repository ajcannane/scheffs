export interface GalleryImage {
  fullSrc: string;
  thumbSrc: string;
  width: number;
  height: number;
  caption: string;
}

export interface GalleryCategory {
  id: string;
  title: string;
  images: GalleryImage[];
}

function img(
  category: string,
  file: string,
  w: number,
  h: number,
  thumb: string,
  caption?: string,
): GalleryImage {
  return {
    fullSrc: `/images/${category}/${file}`,
    thumbSrc: `/images/${category}/${thumb}`,
    width: w,
    height: h,
    caption: caption ?? category,
  };
}

export const galleryData: GalleryCategory[] = [
  {
    id: 'kitchens',
    title: 'Kitchen',
    images: [
      img('Kitchen', '15_960x720.jpg',  960, 720, '15_320x240.jpg'),
      img('Kitchen', '16_960x720.jpg',  960, 720, '16_320x240.jpg'),
      img('Kitchen', '45_960x720.jpg',  960, 720, '45_180x135.jpg'),
      img('Kitchen', '18_960x720.jpg',  960, 720, '18_320x240.jpg'),
      img('Kitchen', '17_959x720.jpg',  959, 720, '17_320x240.jpg'),
      img('Kitchen', '46_960x720.jpg',  960, 720, '46_180x135.jpg'),
      img('Kitchen', '19_500x375.jpg',  500, 375, '19_320x240.jpg'),
      img('Kitchen', '21_960x719.jpg',  960, 719, '21_320x240.jpg'),
      img('Kitchen', '22_960x720.jpg',  960, 720, '22_320x240.jpg'),
      img('Kitchen', '23_960x720.jpg',  960, 720, '23_320x240.jpg'),
      img('Kitchen', '24_959x720.jpg',  959, 720, '24_320x240.jpg'),
      img('Kitchen', '25_640x479.jpg',  640, 479, '25_320x240.jpg'),
      img('Kitchen', '26_500x375.jpg',  500, 375, '26_320x240.jpg'),
      img('Kitchen', '27_960x720.jpg',  960, 720, '27_320x240.jpg'),
      img('Kitchen', '29_959x720.jpg',  959, 720, '29_320x240.jpg'),
      img('Kitchen', '30_960x720.jpg',  960, 720, '30_320x240.jpg'),
      img('Kitchen', '31_960x719.jpg',  960, 719, '31_320x240.jpg'),
      img('Kitchen', '32_960x720.jpg',  960, 720, '32_320x240.jpg'),
      img('Kitchen', '33_854x480.jpg',  854, 480, '33_320x180.jpg'),
      img('Kitchen', '34_960x720.jpg',  960, 720, '34_180x135.jpg'),
      img('Kitchen', '35_960x720.jpg',  960, 720, '35_180x135.jpg'),
      img('Kitchen', '36_960x720.jpg',  960, 720, '36_320x240.jpg'),
      img('Kitchen', '37_960x540.jpg',  960, 540, '37_320x180.jpg'),
      img('Kitchen', '39_960x720.jpg',  960, 720, '39_180x135.jpg'),
      img('Kitchen', '41_960x720.jpg',  960, 720, '41_320x240.jpg'),
      img('Kitchen', '44_960x720.jpg',  960, 720, '44_180x135.jpg'),
      img('Kitchen', '20_540x720.jpg',  540, 720, '20_180x240.jpg'),
      img('Kitchen', '28_540x720.jpg',  540, 720, '28_180x240.jpg'),
      img('Kitchen', '38_405x720.jpg',  405, 720, '38_135x240.jpg'),
      img('Kitchen', '40_540x720.jpg',  540, 720, '40_180x240.jpg'),
      img('Kitchen', '42_540x720.jpg',  540, 720, '42_180x240.jpg'),
      img('Kitchen', '43_540x720.jpg',  540, 720, '43_180x240.jpg'),
    ],
  },
  {
    id: 'benchtop',
    title: 'Benchtop',
    images: [
      img('Benchtop', '1_960x720.jpg',  960, 720, '1_320x240.jpg'),
      img('Benchtop', '2_960x720.jpg',  960, 720, '2_320x240.jpg'),
      img('Benchtop', '3_524x392.jpg',  524, 392, '3_320x239.jpg'),
    ],
  },
  {
    id: 'doors',
    title: 'Doors & Drawers',
    images: [
      img('DoorsAndDrawers', '4_960x720.jpg',  960, 720, '4_320x240.jpg',  'Doors & Drawers'),
      img('DoorsAndDrawers', '5_960x719.jpg',  960, 719, '5_320x240.jpg',  'Doors & Drawers'),
      img('DoorsAndDrawers', '6_960x719.jpg',  960, 719, '6_320x240.jpg',  'Doors & Drawers'),
      img('DoorsAndDrawers', '7_960x720.jpg',  960, 720, '7_320x240.jpg',  'Doors & Drawers'),
      img('DoorsAndDrawers', '8_898x674.jpg',  898, 674, '8_320x240.jpg',  'Doors & Drawers'),
      img('DoorsAndDrawers', '9_959x720.jpg',  959, 720, '9_320x240.jpg',  'Doors & Drawers'),
      img('DoorsAndDrawers', '10_960x720.jpg', 960, 720, '10_320x240.jpg', 'Doors & Drawers'),
      img('DoorsAndDrawers', '11_540x720.jpg', 540, 720, '11_180x240.jpg', 'Doors & Drawers'),
      img('DoorsAndDrawers', '12_640x479.jpg', 640, 479, '12_320x240.jpg', 'Doors & Drawers'),
      img('DoorsAndDrawers', '13_500x375.jpg', 500, 375, '13_320x240.jpg', 'Doors & Drawers'),
      img('DoorsAndDrawers', '14_960x720.jpg', 960, 720, '14_320x240.jpg', 'Doors & Drawers'),
      img('DoorsAndDrawers', '15_960x720.jpg', 960, 720, '15_320x240.jpg', 'Doors & Drawers'),
    ],
  },
  {
    id: 'pantry',
    title: 'Pantry',
    images: [
      img('Pantry', '42_960x719.jpg', 960, 719, '42_320x240.jpg'),
      img('Pantry', '43_960x720.jpg', 960, 720, '43_320x240.jpg'),
      img('Pantry', '44_960x720.jpg', 960, 720, '44_320x240.jpg'),
      img('Pantry', '45_960x720.jpg', 960, 720, '45_320x240.jpg'),
      img('Pantry', '46_540x720.jpg', 540, 720, '46_180x240.jpg'),
      img('Pantry', '47_540x720.jpg', 540, 720, '47_180x240.jpg'),
    ],
  },
  {
    id: 'vanity',
    title: 'Vanity',
    images: [
      img('Vanity', '47_549x412.jpg', 549, 412, '47_320x240.jpg'),
      img('Vanity', '48_960x720.jpg', 960, 720, '48_320x240.jpg'),
      img('Vanity', '49_960x720.jpg', 960, 720, '49_320x240.jpg'),
      img('Vanity', '50_960x720.jpg', 960, 720, '50_320x240.jpg'),
      img('Vanity', '51_540x720.jpg', 540, 720, '51_180x240.jpg'),
      img('Vanity', '52_540x720.jpg', 540, 720, '52_180x240.jpg'),
      img('Vanity', '53_375x500.jpg', 375, 500, '53_180x240.jpg'),
      img('Vanity', '54_540x720.jpg', 540, 720, '54_180x240.jpg'),
      img('Vanity', '55_540x720.jpg', 540, 720, '55_180x240.jpg'),
    ],
  },
  {
    id: 'wallunit',
    title: 'Wall & Entertainment Units',
    images: [
      img('WallUnit', '54_439x330.jpg', 439, 330, '54_319x240.jpg', 'Wall Unit'),
      img('WallUnit', '55_960x720.jpg', 960, 720, '55_320x240.jpg', 'Wall Unit'),
      img('WallUnit', '56_959x720.jpg', 959, 720, '56_320x240.jpg', 'Wall Unit'),
      img('WallUnit', '57_540x720.jpg', 540, 720, '57_180x240.jpg', 'Wall Unit'),
      img('WallUnit', '58_540x720.jpg', 540, 720, '58_180x240.jpg', 'Wall Unit'),
    ],
  },
  {
    id: 'wardrobe',
    title: 'Wardrobe',
    images: [
      img('Wardrobe', '59_960x720.jpg', 960, 720, '59_320x240.jpg'),
      img('Wardrobe', '60_540x720.jpg', 540, 720, '60_180x240.jpg'),
      img('Wardrobe', '61_540x720.jpg', 540, 720, '61_180x240.jpg'),
    ],
  },
  {
    id: 'workshop',
    title: 'Workshop',
    images: [
      img('Workshop', 'Vehicle_960x720.jpg',  960, 720, 'Vehicle_320x240.jpg',  'Vehicle'),
      img('Workshop', 'workshop_770x434.jpg',  770, 434, 'workshop_320x180.jpg',  'Workshop'),
      img('Workshop', 'workshop2_757x426.jpg', 757, 426, 'workshop2_320x180.jpg', 'Workshop'),
    ],
  },
];
